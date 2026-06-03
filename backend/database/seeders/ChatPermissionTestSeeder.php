<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChatPermissionTestSeeder extends Seeder
{
    public function run(): void
    {

        DB::beginTransaction();
        try {

            // 1. Ensure you have your main active testing user (Change 'student' to your test target role)
            $me = User::find(1) ?? User::factory()->create([
                'id' => 1,
                'name' => 'Shafeeque (Me)',
                'acc_type' => 'student'
            ]);

            $roles = ['admin', 'teacher', 'staff', 'student'];
            $messageTypes = ['text', 'image', 'video', 'audio', 'file', 'voice', 'audio'];

            $sampleTexts = [
                "Hello! Let me know if you received the files.",
                "Can you verify my submission status?",
                "Yes, the update is fine. Let's process it.",
                "Please review the attached material.",
                "This is a text bubble containing normal conversational length tags.",
                "Stretching your layout strings by sending a remarkably long text node intended to test how your Flutter custom ChatBubble constraints wrap boundaries near device screen margins."
            ];

            $sampleMedia = [
                'image' => 'https://picsum.photos/800/600',
                'video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'audio' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'voice' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
                'file'  => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf', // Mapped to PDF
            ];

            $this->command->info('Generating 20 structured single and group contexts...');

            // 2. Loop to build exactly 20 distinct rooms
            for ($roomIndex = 1; $roomIndex <= 20; $roomIndex++) {

                // Alternately create 15 single chats and 5 group chats
                $isGroup = ($roomIndex > 15);

                if (!$isGroup) {
                    // Target role rotations for single rooms
                    $peerRole = $roles[($roomIndex - 1) % count($roles)];

                    $peer = User::factory()->create([
                        'name' => "Test " . ucfirst($peerRole) . " ($roomIndex)",
                        'acc_type' => $peerRole
                    ]);

                    $conversation = Conversation::create([
                        'type' => 'single',
                        'status' => 'active',
                        'created_by' => $me->id
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $me->id,
                        'is_pinned' => ($roomIndex <= 3), // Pin first 3 rooms to test sorting
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $peer->id,
                    ]);

                    $participantsList = [$me->id, $peer->id];
                } else {
                    // Group Chat Generation
                    $conversation = Conversation::create([
                        'type' => 'group',
                        'title' => "Batch Core Group Delta " . ($roomIndex - 15),
                        'status' => 'active',
                        'created_by' => $me->id
                    ]);

                    // Register Me
                    ConversationParticipant::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $me->id,
                        'is_muted' => ($roomIndex === 16), // Mute one room to test UI status icons
                    ]);

                    $participantsList = [$me->id];

                    // Add 4 additional peers with mixed roles into the group
                    foreach ($roles as $gRole) {
                        $gUser = User::factory()->create([
                            'name' => "Group Member " . ucfirst($gRole),
                            'acc_type' => $gRole
                        ]);
                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id' => $gUser->id,
                        ]);
                        $participantsList[] = $gUser->id;
                    }
                }

                // 3. Populate each room with up to 100 Chronological Messages
                $currentTimeMarker = Carbon::now()->subDays(6);

                for ($msgIndex = 1; $msgIndex <= 100; $msgIndex++) {
                    // Select a random sender out of the room participants
                    $senderId = $participantsList[array_rand($participantsList)];

                    // Keep 75% of items as clean text, otherwise cycle media items
                    $type = (rand(1, 4) > 1) ? 'text' : $messageTypes[array_rand($messageTypes)];

                    $body = null;
                    $mediaUrl = null;
                    $mediaMeta = null;

                    if ($type === 'text') {
                        $body = $sampleTexts[array_rand($sampleTexts)] . " (#$msgIndex)";
                    } else {
                        $mediaUrl = $sampleMedia[$type];
                        $body = "Shared a system " . ($type === 'file' ? 'PDF document' : $type) . " (#$msgIndex)";
                        $mediaMeta = [
                            'size' => rand(700000, 9500000),
                            'file_name' => "attachment_test_$msgIndex." . ($type === 'file' ? 'pdf' : ($type === 'image' ? 'jpg' : 'mp3')),
                            'duration' => in_array($type, ['audio', 'voice', 'video']) ? rand(8, 180) : null
                        ];
                    }

                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_id'       => $senderId,
                        'message'         => $body,
                        'type'            => $type,
                        'media_url'       => $mediaUrl,
                        // 'media_meta'      => $mediaMeta,
                        'created_at'      => $currentTimeMarker->copy()->addMinutes($msgIndex * rand(12, 50)),
                        'updated_at'      => $currentTimeMarker,
                    ]);
                }
            }
            DB::commit();
            $this->command->info('Successfully seeded 20 permission-variant conversational contexts.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding chat permissions test data: ' . $e->getMessage());
        }
    }
}
