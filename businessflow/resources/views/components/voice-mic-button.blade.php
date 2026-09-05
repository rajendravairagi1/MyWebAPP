{{--
    Drop next to any input/textarea by id — click to speak, the
    transcribed text is appended into that field. Uses the browser's
    built-in Web Speech API (no library, no server round-trip, no cost),
    so it quietly hides itself on browsers that don't support it (mainly
    desktop Safari/Firefox) rather than showing a broken button.
--}}
@props(['target'])

<button type="button"
    x-data="{
        listening: false,
        supported: 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window,
        start() {
            if (! this.supported || this.listening) return;

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.lang = '{{ match (app()->getLocale()) {
                'hi', 'hi-en' => 'hi-IN',
                'gu' => 'gu-IN',
                'es' => 'es-ES',
                default => 'en-IN',
            } }}';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = () => { this.listening = true; };
            recognition.onend = () => { this.listening = false; };
            recognition.onerror = () => { this.listening = false; };
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const field = document.getElementById('{{ $target }}');
                if (! field) return;
                field.value = field.value.trim() ? field.value.trim() + ' ' + transcript : transcript;
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.focus();
            };

            recognition.start();
        },
    }"
    x-show="supported"
    x-cloak
    @click="start()"
    :class="listening ? 'bg-red-600 text-white animate-pulse' : 'bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-500'"
    :title="listening ? '{{ __('Listening…') }}' : '{{ __('Speak to fill this field') }}'"
    class="inline-flex items-center justify-center h-9 w-9 rounded-md shrink-0"
>
    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 14a3 3 0 003-3V6a3 3 0 10-6 0v5a3 3 0 003 3z" />
        <path d="M19 11a1 1 0 10-2 0 5 5 0 01-10 0 1 1 0 10-2 0 7 7 0 006 6.93V20H9a1 1 0 100 2h6a1 1 0 100-2h-2v-2.07A7 7 0 0019 11z" />
    </svg>
</button>
