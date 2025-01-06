<?php


if (!function_exists('json_response')) {
	function json_response($resp_code = 200, $msg = '', $data = array())
	{
		$success = false;
		if ($resp_code == 200) {
			$success = true;
		}
		return response([
			'success'	=> $success,
			'message'	=> $msg,
			'data'		=> $data
		], $resp_code);
	}
}

if (!function_exists('subscriptionID')) {
	function subscriptionID($uuid)
	{
		return App\Models\Subscription::findByUUIDOrFail($uuid)->id;
	}
}

if (!function_exists('getIdByUUID')) {
    function getIdByUUID(string $uuid, string $modelName): int
    {
        $modelClass = '\\App\\Models\\Tenant\\' . ucfirst($modelName);

        if (!class_exists($modelClass)) {
            throw new InvalidArgumentException("Model {$modelName} does not exist.");
        }
        return $modelClass::findByUUIDOrFail($uuid)->id;
    }
}

if (!function_exists('userId')) {
	function userId($uuid)
	{
		return App\Models\Tenant\User::findByUUIDOrFail($uuid);
	}
}

if (!function_exists('ext_base64')) {
    function ext_base64($base64)
    {
        if (!is_string($base64))
            return false;
        return explode('/', mime_content_type($base64))[1];
    }
}

if (!function_exists('is_base64')) {
    function is_base64($s)
    {
        $s = explode(',', $s)[1];
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if (false === $decoded) return false;

        // Encode the string again
        if (base64_encode($decoded) != $s) return false;

        return true;
    }
}