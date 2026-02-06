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
     * Check if git is available on the system
     */
    private function isGitAvailable()
    {
        $gitPath = ROOTPATH;
        
        // Check if .git directory exists
        if (!is_dir($gitPath . '.git')) {
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

        try {
            // Change to git directory and execute git log
            $command = "cd " . escapeshellarg($gitPath) . " && git log --all --pretty=format:'%H|%an|%ae|%ad|%s' --date=iso -" . (int)$limit;
            
            $output = shell_exec($command);
            
            if ($output === null) {
                return [];
            }

            $lines = explode("\n", trim($output));
            
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }
                
                $parts = explode('|', $line, 5);
                
                if (count($parts) === 5) {
                    $commits[] = [
                        'hash' => $parts[0],
                        'short_hash' => substr($parts[0], 0, 7),
                        'author' => $parts[1],
                        'email' => $parts[2],
                        'date' => $parts[3],
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
