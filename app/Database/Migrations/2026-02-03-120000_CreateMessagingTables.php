<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagingTables extends Migration
{
    public function up()
    {
        // Conversations
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'company_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_by' => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('conversations', true);

        // Conversation participants
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'last_read_at' => ['type' => 'DATETIME', 'null' => true],
            'added_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['conversation_id', 'user_id']);
        $this->forge->createTable('conversation_participants', true);

        // Messages
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'conversation_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'sender_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'body' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('conversation_id');
        $this->forge->addKey('sender_id');
        $this->forge->createTable('messages', true);

        // Notifications (in-app)
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'company_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'notification_type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'message' => ['type' => 'TEXT', 'null' => true],
            'related_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'related_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'priority' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'medium'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'is_read' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'company_id']);
        $this->forge->addKey('notification_type');
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('messages', true);
        $this->forge->dropTable('conversation_participants', true);
        $this->forge->dropTable('conversations', true);
    }
}
