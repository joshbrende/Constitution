<?php

namespace App\Services;

use App\Models\Course;
use App\Models\DialogueChannel;
use App\Models\DialogueThread;
use App\Models\LibraryDocument;
use App\Models\MemberNotificationCampaign;
use App\Models\Role;
use App\Models\Section;
use App\Models\SectionVersion;
use Illuminate\Database\Eloquent\Model;

class MemberAutoNotificationService
{
    public const TRIGGER_COURSE_PUBLISHED = 'course.published';

    public const TRIGGER_COURSE_UPDATED = 'course.updated';

    public const TRIGGER_CONSTITUTION_PUBLISHED = 'constitution.section_published';

    public const TRIGGER_LIBRARY_PUBLISHED = 'library.document_published';

    public const TRIGGER_DIALOGUE_THREAD_STARTED = 'dialogue.thread_started';

    public function __construct(
        protected MemberNotificationDispatchService $dispatch,
    ) {}

    public function coursePublished(Course $course, bool $isNew = false): ?int
    {
        if ($course->status !== 'published') {
            return null;
        }

        $trigger = $isNew ? self::TRIGGER_COURSE_PUBLISHED : self::TRIGGER_COURSE_PUBLISHED;
        if ($this->alreadyDispatched($trigger, $course)) {
            return null;
        }

        $label = $course->grants_membership ? 'Membership course' : 'Academy course';

        return $this->dispatch($trigger, $course, [
            'title' => $isNew ? "New {$label}: {$course->title}" : "{$label} now available: {$course->title}",
            'body' => $this->trimBody($course->description)
                ?: 'A new learning path is available in the Academy. Enrol and start learning.',
            'cta_label' => 'View course',
            'cta_tab' => 'HomeTab',
            'cta_screen' => 'CourseDetail',
            'cta_params' => ['courseId' => $course->id],
        ]);
    }

    public function courseUpdated(Course $course): ?int
    {
        if ($course->status !== 'published') {
            return null;
        }

        return $this->dispatch(self::TRIGGER_COURSE_UPDATED.'_'.now()->timestamp, $course, [
            'title' => "Course updated: {$course->title}",
            'body' => $this->trimBody($course->description)
                ?: 'An Academy course you can enrol in has been updated. Open it to see what is new.',
            'cta_label' => 'Open course',
            'cta_tab' => 'HomeTab',
            'cta_screen' => 'CourseDetail',
            'cta_params' => ['courseId' => $course->id],
        ]);
    }

    public function constitutionSectionPublished(Section $section, SectionVersion $version): ?int
    {
        $section->loadMissing('chapter');
        $doc = $section->chapter?->constitution_slug ?? 'zanupf';
        $docLabel = match ($doc) {
            'amendment3' => 'Amendment Bill',
            'zimbabwe' => 'Zimbabwe Constitution',
            default => 'ZANU PF Constitution',
        };

        $label = $doc === 'amendment3' ? 'Clause' : ($doc === 'zimbabwe' ? 'Section' : 'Article');
        $reference = trim($section->logical_number.' – '.$section->title);

        $sourceKey = $section;
        $trigger = self::TRIGGER_CONSTITUTION_PUBLISHED.'_'.$version->id;

        if ($this->alreadyDispatched($trigger, $sourceKey)) {
            return null;
        }

        return $this->dispatch($trigger, $sourceKey, [
            'title' => "{$docLabel} update: {$label} {$section->logical_number}",
            'body' => "{$reference} has been published. Open the reader to review the latest text.",
            'cta_label' => 'Read now',
            'cta_tab' => 'ConstitutionTab',
            'cta_screen' => 'ConstitutionList',
            'cta_params' => ['initialDoc' => $doc],
        ]);
    }

    public function libraryDocumentPublished(LibraryDocument $document, bool $isNew = false): ?int
    {
        if ($document->published_at === null) {
            return null;
        }

        $trigger = $isNew ? self::TRIGGER_LIBRARY_PUBLISHED : self::TRIGGER_LIBRARY_PUBLISHED;
        if ($this->alreadyDispatched($trigger, $document)) {
            return null;
        }

        $audienceType = match ($document->access_rule) {
            'leadership' => MemberNotificationCampaign::AUDIENCE_ROLE,
            default => MemberNotificationCampaign::AUDIENCE_ALL,
        };

        $roleId = null;
        if ($audienceType === MemberNotificationCampaign::AUDIENCE_ROLE) {
            $roleId = \App\Models\Role::where('slug', 'presidium')->value('id')
                ?? \App\Models\Role::where('slug', 'cadre_designee')->value('id');
            if (! $roleId) {
                return null;
            }
        }

        return $this->dispatch($trigger, $document, [
            'title' => $isNew ? "New library document: {$document->title}" : "Library update: {$document->title}",
            'body' => $this->trimBody($document->abstract)
                ?: 'A document has been added to the Digital Library.',
            'cta_label' => 'Open document',
            'cta_tab' => 'HomeTab',
            'cta_screen' => 'LibraryDocument',
            'cta_params' => ['documentId' => $document->id],
            'audience_type' => $audienceType,
            'role_id' => $roleId,
        ]);
    }

    public function dialogueThreadStarted(DialogueChannel $channel, DialogueThread $thread, string $openingMessage): ?int
    {
        if ($this->alreadyDispatched(self::TRIGGER_DIALOGUE_THREAD_STARTED, $thread)) {
            return null;
        }

        $audienceType = MemberNotificationCampaign::AUDIENCE_ALL;
        $roleId = null;

        if ($channel->min_role_slug) {
            $roleId = Role::query()->where('slug', $channel->min_role_slug)->value('id');
            if ($roleId) {
                $audienceType = MemberNotificationCampaign::AUDIENCE_ROLE;
            }
        }

        $preview = $this->trimBody($openingMessage, 140)
            ?: 'An editor has invited members to join the conversation.';

        return $this->dispatch(self::TRIGGER_DIALOGUE_THREAD_STARTED, $thread, [
            'title' => "New chat: {$thread->title}",
            'body' => "Editor started a conversation in {$channel->name}. {$preview}",
            'cta_label' => 'Join chat',
            'cta_tab' => 'ChatTab',
            'cta_screen' => 'ChatThread',
            'cta_params' => [
                'channel' => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'slug' => $channel->slug,
                ],
                'thread' => [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'status' => $thread->status,
                ],
            ],
            'audience_type' => $audienceType,
            'role_id' => $roleId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function dispatch(string $trigger, Model $source, array $payload): ?int
    {
        $campaign = MemberNotificationCampaign::create([
            'title' => $payload['title'],
            'body' => $payload['body'],
            'audience_type' => $payload['audience_type'] ?? MemberNotificationCampaign::AUDIENCE_ALL,
            'province_id' => $payload['province_id'] ?? null,
            'role_id' => $payload['role_id'] ?? null,
            'cta_type' => 'internal',
            'cta_label' => $payload['cta_label'] ?? null,
            'cta_tab' => $payload['cta_tab'] ?? null,
            'cta_screen' => $payload['cta_screen'] ?? null,
            'cta_params' => $payload['cta_params'] ?? null,
            'cta_url' => null,
            'status' => MemberNotificationCampaign::STATUS_DRAFT,
            'trigger' => $trigger,
            'source_type' => $source::class,
            'source_id' => $source->getKey(),
            'created_by_user_id' => auth()->id(),
        ]);

        return $this->dispatch->publish($campaign);
    }

    protected function alreadyDispatched(string $trigger, Model $source): bool
    {
        return MemberNotificationCampaign::query()
            ->where('trigger', $trigger)
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->where('status', MemberNotificationCampaign::STATUS_PUBLISHED)
            ->exists();
    }

    protected function trimBody(?string $text, int $limit = 400): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $plain = trim(strip_tags($text));

        return mb_strlen($plain) > $limit ? mb_substr($plain, 0, $limit - 1).'…' : $plain;
    }
}
