# OIDC

OpenID Connect 的认证插件

## 功能概览

- 支持通过 OIDC（授权码流程）登录 Typecho
- 支持首次登录自动注册（可指定新用户组）
- 支持已登录本地账户后进行 OIDC 绑定
- 支持禁用 Typecho 原生登录/注册页并统一跳转到 OIDC 登录页
- 支持控制是否允许用户在绑定页执行解绑（默认不允许）

## 安装

```bash
cd typecho/usr/plugins
git clone https://github.com/CertStone/typecho-oidc.git Oidc
```

## 使用

启用插件并配置好后，在需要的位置添加指向 `oidc/login` 的按钮即可。
如果需要完整的自定义登录页，可使用 `oidc/login-page`。

比如 `sidebar.php`

```php
<li><a href="<?php $this->options->index('oidc/login'); ?>"><?php _e('单点登录'); ?></a></li>
```

或 `login.php`

```php
<a href="<?php $options->index('oidc/login'); ?>"><?php _e('单点登录'); ?></a>
```

自定义登录页示例：

```php
<a href="<?php $options->index('oidc/login-page'); ?>"><?php _e('统一认证登录'); ?></a>
```

> 注意：若在插件设置中关闭「自动注册」，OIDC 首次登录用户需要先使用本地账号登录并在 OIDC 绑定管理页完成绑定。

## 插件配置项说明（后台）

### 必填项

1. `OIDC 发现文档 URL`
   - 例如：`https://idp.example.com/.well-known/openid-configuration`
2. `Client ID`
3. `Client Secret`

### 常用项

- `OIDC 系统名称`：登录页按钮文案展示名称（如“单点登录”）
- `Scope`：默认 `openid email profile`
- `PKCE 支持`：建议在 IdP 支持时开启
- `自动注册`：开启后，未绑定用户可自动创建 Typecho 账户（要求 `email_verified=true`）
- `OIDC 自动注册用户组`：可选 `subscriber / contributor / editor`
- `是否禁用 Typecho 原生登录和注册页`：开启后，访问 `/admin/login.php` 和 `/admin/register.php` 会跳转到 `/oidc/login-page`
- `是否允许用户解绑 OIDC 账户`：默认“否”，适用于强制 SSO 场景

> 说明：出于安全考虑，自动注册用户组不建议直接使用高权限组。建议先以低权限组接入，再由管理员后台调整权限。

## 强制 SSO 推荐配置

如果你希望用户只能通过 IdP 管理身份，建议如下：

1. 开启插件功能
2. 开启自动注册（建议开启，以避免首次登录用户无本地入口导致无法完成接入）
3. 设置 `OIDC 自动注册用户组`（通常为 `subscriber` 或 `contributor`）
4. 开启 `是否禁用 Typecho 原生登录和注册页`
5. 保持 `是否允许用户解绑 OIDC 账户 = 否`

这样可以实现：
- 原生登录/注册页不可用
- 账号生命周期由 IdP + OIDC 绑定关系主导
- 用户无法自行解除绑定绕过 SSO 策略

## IdP 配置说明

根据插件代码，当前 OIDC 回调地址固定为：

```
<你的站点地址>/oidc/callback
```

请将该地址加入 IdP 的 **Redirect URIs**。

建议同时配置：

- **Allowed Grant Types**：`authorization_code`（开启 PKCE 时也允许 PKCE）
- **Client Authentication**：`client_secret_basic`（与当前插件实现一致）
- **Scopes**：至少包含 `openid email profile`

为保证自动注册可用，IdP 侧应提供：

- `sub`（必须）：唯一用户标识
- `iss`（建议/通常由 discovery 提供）
- `email`（用于 Typecho mail）
- `email_verified=true`（自动注册前置条件）
- `name`（可选，映射 Typecho screenName）
- `website`（可选，映射 Typecho url）

本插件目前 **未实现 OIDC 退出登录（end_session_endpoint）**，因此 **Post Logout Redirect URIs** 不会被使用。
如果你在 IdP 中需要填写，可使用以下地址作为默认回跳：

```
<你的站点地址>/
```

自定义登录页入口（用于展示现代化登录页）：

```
<你的站点地址>/oidc/login-page
```

## 权限与用户组建议

- `subscriber`：最小权限，适合普通成员
- `contributor`：可写草稿，不能直接发布
- `editor`：可管理内容
- `administrator`：最高权限（强烈不建议作为自动注册默认组）

建议默认使用低权限组，再通过后台审批或同步策略提升权限。
