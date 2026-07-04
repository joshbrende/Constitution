<button type="submit" class="btn-next" @disabled(!empty($disabled)) @if(!empty($form)) form="{{ $form }}" @endif>
    {{ $label ?? 'Continue' }}
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>
