<?php
namespace TypechoPlugin\Oidc;

use Typecho\Common;
use Widget\Options;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$options = Options::alloc();
$pluginConfig = $options->plugin('Oidc');
$systemName = !empty($pluginConfig->oidcSystemName) ? $pluginConfig->oidcSystemName : 'OIDC';
$nativeAuthDisabled = !empty($pluginConfig->disableNativeAuthPages) && $pluginConfig->disableNativeAuthPages === '1';
$loginAction = $nativeAuthDisabled ? Common::url('/oidc/login', $options->index) : $options->loginAction;
$referer = $options->adminUrl;
$loginUrl = Common::url('/oidc/login', $options->index);
$cdnBase = !empty($pluginConfig->uiCdnBase) ? rtrim($pluginConfig->uiCdnBase, '/') : 'https://s4.zstatic.net';
$backgroundUrl = !empty($pluginConfig->loginBackgroundUrl) ? $pluginConfig->loginBackgroundUrl : '';
$logoUrl = !empty($pluginConfig->loginLogoUrl) ? trim($pluginConfig->loginLogoUrl) : '';
$rememberName = htmlspecialchars(\Typecho\Cookie::get('__typecho_remember_name', ''));
\Typecho\Cookie::delete('__typecho_remember_name');

$noticeType = trim((string) \Typecho\Cookie::get('__typecho_notice_type', ''));
$noticeRaw = (string) \Typecho\Cookie::get('__typecho_notice', '');
$noticeMessages = [];
if ($noticeRaw !== '') {
    $decoded = json_decode($noticeRaw, true);
    if (is_array($decoded)) {
        $noticeMessages = $decoded;
    }
}
\Typecho\Cookie::delete('__typecho_notice');
\Typecho\Cookie::delete('__typecho_notice_type');
$daisyCssUrl = $cdnBase . '/npm/daisyui@5/daisyui.css';
$daisyThemeUrl = $cdnBase . '/npm/daisyui@5/themes.css';
$tailwindBrowserUrl = $cdnBase . '/npm/@tailwindcss/browser@4';
?>
<!DOCTYPE HTML>
<html data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php _e('%s 登录', $systemName); ?> - <?php echo htmlspecialchars($options->title); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?php echo $daisyCssUrl; ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $daisyThemeUrl; ?>" type="text/css" />
    <script src="<?php echo $tailwindBrowserUrl; ?>"></script>
</head>

<body>
    <div class="hero min-h-screen bg-base-200"<?php if (!empty($backgroundUrl)) { ?> style="background-image: url('<?php echo htmlspecialchars($backgroundUrl); ?>'); background-size: cover; background-position: center;"<?php } ?> >
        <?php if (!empty($backgroundUrl)) { ?>
            <div class="hero-overlay bg-base-200/70"></div>
        <?php } ?>
        <div class="hero-content flex-col text-center">
            <div class="w-full max-w-3xl px-4 md:px-6">
                <div class="flex items-center justify-center gap-2 text-sm text-base-content/60 mb-2 min-h-16">
                    <?php if (!empty($logoUrl)) { ?>
                        <div class="avatar">
                            <div class="w-16 h-16 rounded-xl bg-base-100 shadow-sm ring ring-base-300">
                                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($systemName); ?>" class="object-contain" />
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <h1 class="text-4xl font-bold text-base-content mt-2">
                    <?php _e('登录你的账号'); ?>
                </h1>
                <p class="pt-2 pb-3 text-sm text-base-content/60">
                    <?php _e('建议使用 %s', $systemName); ?>
                </p>

                <div class="card bg-base-100 shadow-2xl w-full max-w-3xl mx-auto">
                    <div class="card-body space-y-3 md:space-y-4 p-5 md:p-6">
                        <?php if (!empty($noticeMessages)) { ?>
                            <div class="alert <?php echo $noticeType === 'error' ? 'alert-error' : ($noticeType === 'success' ? 'alert-success' : 'alert-info'); ?>">
                                <span><?php echo htmlspecialchars((string) $noticeMessages[0]); ?></span>
                            </div>
                        <?php } ?>

                        <div id="oidc-login-progress" class="alert alert-info hidden">
                            <span><?php _e('正在登录，请稍候...'); ?></span>
                        </div>

                        <a class="btn btn-primary btn-md w-full" href="<?php echo $loginUrl; ?>">
                            <?php _e('从 %s 登录/注册', $systemName); ?>
                        </a>

                        <?php if (!$nativeAuthDisabled) { ?>
                            <div class="divider text-xs text-base-content/50 my-1"><?php _e('或使用本地账户'); ?></div>

                            <form id="oidc-local-login-form" action="<?php echo htmlspecialchars($loginAction); ?>" method="post" name="login" role="form" class="space-y-3 text-left">
                                <?php if (isset($this->security) && isset($this->request)) { ?>
                                    <input type="hidden" name="_" value="<?php echo htmlspecialchars($this->security->getToken($this->request->getRequestUrl())); ?>" />
                                <?php } ?>
                                <div class="form-control">
                                    <label class="input input-bordered input-md w-full rounded-2xl flex items-center gap-2">
                                        <svg class="w-4 h-4 text-base-content/60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z" />
                                        </svg>
                                        <input type="text" id="name" name="name" value="<?php echo $rememberName; ?>" class="grow text-sm" placeholder="用户名" autocomplete="username" required />
                                    </label>
                                </div>
                                <div class="form-control">
                                    <label class="input input-bordered input-md w-full rounded-2xl flex items-center gap-2">
                                        <svg class="w-4 h-4 text-base-content/60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M17 9h-1V7a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-6 6.73V17a1 1 0 1 0 2 0v-1.27a2 2 0 1 0-2 0ZM10 9V7a2 2 0 0 1 4 0v2Z" />
                                        </svg>
                                        <input type="password" id="password" name="password" class="grow text-sm" placeholder="密码" autocomplete="current-password" required />
                                    </label>
                                </div>
                                <input type="hidden" name="referer" value="<?php echo htmlspecialchars($referer); ?>" />
                                <div class="form-control">
                                    <label class="label cursor-pointer justify-start gap-2">
                                        <input type="checkbox" class="checkbox checkbox-sm" name="remember" value="1" id="remember" />
                                        <span class="label-text"><?php _e('记住我'); ?></span>
                                    </label>
                                </div>
                                <button id="oidc-local-login-submit" type="submit" class="btn btn-neutral btn-md w-full">
                                    <?php _e('登录'); ?>
                                </button>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-warning">
                                <span><?php _e('当前站点已启用强制单点登录，本地账号入口已关闭'); ?></span>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var form = document.getElementById('oidc-local-login-form');
            var submitBtn = document.getElementById('oidc-local-login-submit');
            var progressAlert = document.getElementById('oidc-login-progress');

            if (!form || !submitBtn) {
                return;
            }

            form.addEventListener('submit', function () {
                submitBtn.classList.add('opacity-70', 'cursor-wait');
                submitBtn.textContent = '登录中...';
                submitBtn.disabled = true;
                if (progressAlert) {
                    progressAlert.classList.remove('hidden');
                }
            });
        })();
    </script>
</body>

</html>
