@include('errors.minimal', [
    'code' => 404,
    'title' => __('Page not found'),
    'message' => __("This page doesn't exist — it may have been deleted, or the link is out of date."),
])
