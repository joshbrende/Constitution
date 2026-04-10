@php
    $structured = $course->structured_curriculum;
    $days = $structured['days'] ?? [];
    $trailing = $structured['trailing'] ?? collect();
@endphp

@if($course->units->isEmpty())
<div class="alert alert-info">No units yet. Add units via seeders (e.g. Module1SectionsSeeder, Day2Day3Seeder, ModuleQuizzesSeeder) or implement &quot;Add unit&quot; to create them here.</div>
@else
<div class="card mb-4">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @foreach([1, 2, 3] as $d)
            @php
                $day = $days[$d] ?? null;
                if (!$day) continue;
                $standalones = $day['standalones'] ?? collect();
                $modules = $day['modules'] ?? [];
                ksort($modules);
                $hasAny = $standalones->isNotEmpty() || !empty($modules);
                if (!$hasAny) continue;
            @endphp
            <li class="list-group-item bg-light fw-semibold">DAY {{ $d }}</li>
            @foreach($standalones as $item)
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi {{ $item['icon'] ?? 'bi-file-text' }} text-muted me-2"></i>{{ $item['title'] }}</span>
                <div class="d-flex gap-1">
                    @if(($item['type'] ?? '') === 'quiz')
                    <a href="{{ route('units.quiz.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    @endif
                    <a href="{{ route('units.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            @endforeach
            @foreach($modules as $modNum => $moduleItems)
            <li class="list-group-item bg-light fw-semibold pt-2 pb-1">MODULE {{ $modNum }}</li>
            @foreach($moduleItems as $item)
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi {{ $item['icon'] ?? 'bi-file-text' }} text-muted me-2"></i>{{ $item['title'] }}</span>
                <div class="d-flex gap-1">
                    @if(($item['type'] ?? '') === 'quiz')
                    <a href="{{ route('units.quiz.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    @endif
                    <a href="{{ route('units.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            @endforeach
            @endforeach
            @endforeach
            @if($trailing->isNotEmpty())
            <li class="list-group-item bg-light fw-semibold">END</li>
            @foreach($trailing as $item)
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <span><i class="bi {{ $item['icon'] ?? 'bi-file-text' }} text-muted me-2"></i>{{ $item['title'] }}</span>
                <div class="d-flex gap-1">
                    @if(($item['type'] ?? '') === 'quiz')
                    <a href="{{ route('units.quiz.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-secondary" title="Edit Knowledge Check">Knowledge Check</a>
                    @endif
                    <a href="{{ route('units.edit', [$course, $item['id']]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
</div>
@endif
