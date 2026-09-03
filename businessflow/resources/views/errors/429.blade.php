@include('errors.minimal', [
    'code' => 429,
    'title' => __('Too many requests'),
    'message' => __('Please wait a moment and try again.'),
])
