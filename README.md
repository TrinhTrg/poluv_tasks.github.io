## Clear translation cache và các cache khác:
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear

# Đang kiểm tra format JSON và test translation:
$php -r "json_decode(file_get_contents('lang/en.json')); echo json_last_error() === JSON_ERROR_NONE ? 'en.json: Valid JSON' : 'en.json: ERROR - ' . json_last_error_msg(); echo PHP_EOL;"
$php -r "json_decode(file_get_contents('lang/vi.json')); echo json_last_error() === JSON_ERROR_NONE ? 'vi.json: Valid JSON' : 'vi.json: ERROR - ' . json_last_error_msg(); echo PHP_EOL;"
$php artisan tinker --execute="echo 'Current locale: ' . App::currentLocale() . PHP_EOL; echo 'Translation test: ' . __('auth.welcome_back') . PHP_EOL;"
# Translation test trả về key auth.welcome_back thay vì giá trị "Welcome Back", cho thấy translation chưa hoạt động. Đang kiểm tra và sửa:
$php artisan tinker --execute="echo 'Testing translation...' . PHP_EOL; echo 'Key: auth.welcome_back' . PHP_EOL; echo 'Result: ' . __('auth.welcome_back') . PHP_EOL; echo 'Has translation: ' . (Lang::has('auth.welcome_back') ? 'Yes' : 'No') . PHP_EOL;"
$php artisan tinker --execute="use Illuminate\Support\Facades\Lang; echo 'Available locales: ' . implode(', ', array_keys(Lang::getLoader()->namespaces)) . PHP_EOL; echo 'Current locale: ' . App::currentLocale() . PHP_EOL; echo 'Fallback locale: ' . config('app.fallback_locale') . PHP_EOL;"
# Đang kiểm tra xem Laravel có đọc được file JSON translation không:
$php -r "$data = json_decode(file_get_contents('lang/en.json'), true); echo 'JSON loaded successfully' . PHP_EOL; echo 'Has auth key: ' . (isset($data['auth']) ? 'Yes' : 'No') . PHP_EOL; echo 'Has auth.welcome_back: ' . (isset($data['auth']['welcome_back']) ? 'Yes - ' . $data['auth']['welcome_back'] : 'No') . PHP_EOL;"

php artisan route:list --path=login

# Kiểm tra cấu trúc thư mục lang và xem có file translation ở resources/lang không:
$php artisan tinker --execute="echo 'Lang path: ' . lang_path() . PHP_EOL; echo 'Lang path exists: ' . (file_exists(lang_path()) ? 'Yes' : 'No') . PHP_EOL; echo 'en.json exists: ' . (file_exists(lang_path('en.json')) ? 'Yes' : 'No') . PHP_EOL;"