<?php

namespace App\Controllers;

class GitCommits extends BaseController
{
    public function index()
    {
        // Check if git is available
        $gitAvailable = $this->isGitAvailable();
        
        if (!$gitAvailable) {
            return view('admin/gitcommits/index', [
                'title' => 'Git Commits',
                'error' => 'Git is not available on this system',
                'commits' => []
            ]);
        }

        // Get commits from git log
        $commits = $this->getCommits();

        $data = [
            'title' => 'Git Commits',
            'commits' => $commits,
            'error' => null
        ];

        return view('admin/gitcommits/index', $data);
    }

    /**
     * Check if git repository exists and git command is available
     */
    private function isGitAvailable()
    {
        $gitPath = ROOTPATH;
        
        // Check if .git directory exists
        if (!is_dir($gitPath . '.git')) {
            return false;
        }

        // Check if git command is available
        $output = [];
        $returnCode = 0;
        @exec('git --version 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Get commits from git log
     */
    private function getCommits($limit = 100)
    {
        $commits = [];
        $gitPath = ROOTPATH;

        // Validate limit parameter
        $limit = max(1, min((int)$limit, 500));

        try {
            // Use proc_open for more secure command execution
            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];

            $cwd = rtrim($gitPath, '/');
            $gitCommand = "git log --all --pretty=format:'%H|%an|%ae|%ad|%s' --date=iso -" . $limit;
            
            $process = proc_open($gitCommand, $descriptorspec, $pipes, $cwd);
            
            if (!is_resource($process)) {
                log_message('error', 'Failed to execute git command');
                return [];
            }

            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            
            $returnCode = proc_close($process);
            
            if ($returnCode !== 0) {
                log_message('error', 'Git command failed: ' . $errors);
                return [];
            }

            if (empty($output)) {
                return [];
            }

            $lines = explode("\n", trim($output));
            
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }
                
                $parts = explode('|', $line, 5);
                
                if (count($parts) === 5) {
                    // Validate date format before adding
                    $timestamp = strtotime($parts[3]);
                    if ($timestamp === false) {
                        continue;
                    }

                    $commits[] = [
                        'hash' => $parts[0],
                        'short_hash' => substr($parts[0], 0, 7),
                        'author' => $parts[1],
                        'email' => $parts[2],
                        'date' => $parts[3],
                        'timestamp' => $timestamp,
                        'message' => $parts[4]
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error but continue
            log_message('error', 'Error getting git commits: ' . $e->getMessage());
        }

        return $commits;
    }
}
