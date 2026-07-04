<label @if (! empty($for)) for="{{ $for }}" @endif class="wizard-label">
    <span>{!! $text !!}</span>
    @if (! empty($tip))
        @include('setup.partials.field-tip', ['tip' => $tip, 'aria' => $tipAria ?? null])
    @endif
</label>
