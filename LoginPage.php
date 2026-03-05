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
$loginAction = $options->loginAction;
$referer = Common::url('admin/', $options->index);
$loginUrl = Common::url('/oidc/login', $options->index);
$cdnBase = !empty($pluginConfig->uiCdnBase) ? rtrim($pluginConfig->uiCdnBase, '/') : 'https://s4.zstatic.net';
$backgroundUrl = !empty($pluginConfig->loginBackgroundUrl) ? $pluginConfig->loginBackgroundUrl : '';
$daisyCssUrl = $cdnBase . '/npm/daisyui@5';
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
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "PingFang SC",
                "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
        }

        .oidc-page {
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            background: radial-gradient(circle at top, #e0f2fe 0%, #eef2ff 45%, #f8fafc 100%);
            background-size: cover;
            background-position: center;
        }

        .oidc-overlay {
            position: absolute;
            inset: 0;
            background: rgba(248, 250, 252, 0.72);
            backdrop-filter: blur(10px);
        }

        .oidc-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .oidc-card {
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.2);
        }

        .oidc-brand-dot {
            width: 8px;
            height: 8px;
        }
    </style>
</head>

<body>
    <div class="oidc-page"<?php if (!empty($backgroundUrl)) { ?> style="background-image: url('<?php echo htmlspecialchars($backgroundUrl); ?>');"<?php } ?> >
        <div class="oidc-overlay"></div>
        <div class="oidc-shell">
            <div class="text-center">
                <div class="flex items-center justify-center gap-2 text-sm text-base-content/60">
                    <span class="oidc-brand-dot rounded-full bg-primary"></span>
                    <span><?php _e('统一认证'); ?></span>
                </div>
                <h1 class="text-3xl font-semibold text-base-content mt-3">
                    <?php _e('登录你的账号'); ?>
                </h1>
                <p class="text-sm text-base-content/60 mt-2">
                    <?php _e('使用 %s 账户或本地账号登录', $systemName); ?>
                </p>
            </div>

            <div class="card bg-base-100/80 backdrop-blur-sm oidc-card mt-6">
                <div class="card-body space-y-5">
                    <a class="btn btn-primary w-full" href="<?php echo $loginUrl; ?>">
                        <?php _e('从 %s 登录/注册', $systemName); ?>
                    </a>

                    <div class="divider text-xs text-base-content/50"><?php _e('或使用本地账户'); ?></div>

                    <form action="<?php echo $loginAction; ?>" method="post" name="login" role="form" class="space-y-3">
                        <div class="form-control">
                            <label class="label" for="name">
                                <span class="label-text"><?php _e('用户名或邮箱'); ?></span>
                            </label>
                            <input type="text" id="name" name="name" class="input input-bordered" placeholder="<?php _e('请输入用户名或邮箱'); ?>" />
                        </div>
                        <div class="form-control">
                            <label class="label" for="password">
                                <span class="label-text"><?php _e('密码'); ?></span>
                            </label>
                            <input type="password" id="password" name="password" class="input input-bordered" placeholder="<?php _e('请输入密码'); ?>" required />
                        </div>
                        <input type="hidden" name="referer" value="<?php echo $referer; ?>" />
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" class="checkbox checkbox-sm" name="remember" value="1" id="remember" />
                                <span class="label-text"><?php _e('记住我'); ?></span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-neutral w-full">
                            <?php _e('登录'); ?>
                        </button>
                    </form>

                    <p class="text-xs text-center text-base-content/50">
                        <?php _e('注册功能已在后台禁用'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
