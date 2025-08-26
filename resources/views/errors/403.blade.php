@include('errors._template', [
  'code' => 403,
  'title' => "You don't have permission to view this page.",
  'message' => "Please check your account’s access or contact an administrator."
])
