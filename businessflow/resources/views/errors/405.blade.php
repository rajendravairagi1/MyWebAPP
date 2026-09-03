@include('errors.minimal', [
    'code' => 405,
    'title' => __('Not available here'),
    'message' => __('This action is not available from a bookmarked or stale link — go back to your dashboard and try again.'),
])
