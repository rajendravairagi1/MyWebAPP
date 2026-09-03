@include('errors.minimal', [
    'code' => 503,
    'title' => __('Down for maintenance'),
    'message' => __("We'll be back shortly. Please try again in a few minutes."),
])
