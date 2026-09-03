@include('errors.minimal', [
    'code' => 419,
    'title' => __('Session expired'),
    'message' => __('Your session timed out for security. Please go back and try again.'),
])
