<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>وضعیت درخواست پروژه</title>
</head>
<body style="margin:0;padding:24px;background:#071329;color:#F7F9FF;font-family:Tahoma,Vazirmatn,sans-serif;line-height:1.9;">
    <div style="max-width:760px;margin:0 auto;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.10);border-radius:24px;padding:24px;">
        <h1 style="margin:0 0 12px;font-size:24px;color:#45C8FF;">وضعیت درخواست پروژه شما</h1>

        <p style="margin:0 0 16px;color:rgba(247,249,255,.72);">
            کد پیگیری: <strong dir="ltr" style="color:#45C8FF;">{{ $request->tracking_code }}</strong>
        </p>

        <p style="margin:0 0 20px;color:rgba(247,249,255,.78);">
            وضعیت فعلی درخواست شما:
            <strong style="color:#F7F9FF;">{{ $request->statusLabel() }}</strong>
        </p>

        @if ($oldStatus)
            <p style="margin:0 0 20px;color:rgba(247,249,255,.58);">
                وضعیت قبلی: {{ \App\Models\ProjectRequest::statusOptions()[$oldStatus] ?? $oldStatus }}
            </p>
        @endif

        <p style="margin:0 0 24px;color:rgba(247,249,255,.72);">
            برای مشاهده جزئیات و مسیر پیشرفت درخواست، روی دکمه زیر بزن.
        </p>

        <p style="margin:0;">
            <a href="{{ route('request-project.show', $request->tracking_code) }}"
               style="display:inline-block;padding:12px 18px;border-radius:14px;background:#1769FF;color:#fff;text-decoration:none;font-weight:bold;">
                مشاهده وضعیت درخواست
            </a>
        </p>
    </div>
</body>
</html>