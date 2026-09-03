@include('errors.minimal', [
    'code' => 500,
    'title' => __('Something went wrong'),
    'message' => __("This wasn't your fault — please try again, or go back to your dashboard."),
])
