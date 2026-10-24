<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>درخواست پروژه جدید</title>
</head>
<body style="margin:0;padding:24px;background:#071329;color:#F7F9FF;font-family:Tahoma,Vazirmatn,sans-serif;line-height:1.9;">
    <div style="max-width:760px;margin:0 auto;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.10);border-radius:24px;padding:24px;">
        <h1 style="margin:0 0 12px;font-size:24px;color:#45C8FF;">درخواست پروژه جدید ثبت شد</h1>

        <p style="margin:0 0 20px;color:rgba(247,249,255,.72);">
            کد پیگیری: <strong dir="ltr" style="color:#45C8FF;">{{ $request->tracking_code }}</strong>
        </p>

        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">نام</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);">{{ $request->name }}</td>
            </tr>
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">تلفن</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);" dir="ltr">{{ $request->phone }}</td>
            </tr>
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">ایمیل</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);" dir="ltr">{{ $request->email ?: '—' }}</td>
            </tr>
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">خدمت</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);">{{ $request->service }}</td>
            </tr>
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">بودجه</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);">{{ $request->budgetLabel() }}</td>
            </tr>
            <tr>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);color:rgba(247,249,255,.55);">زمان‌بندی</td>
                <td style="padding:8px;border-bottom:1px solid rgba(255,255,255,.08);">{{ $request->timelineLabel() }}</td>
            </tr>
        </table>

        <p style="margin:24px 0 0;">
            <a href="{{ route('admin.project-requests.show', ['id' => $request->id]) }}"
               style="display:inline-block;padding:12px 18px;border-radius:14px;background:#1769FF;color:#fff;text-decoration:none;font-weight:bold;">
                مشاهده در پنل مدیریت
            </a>
        </p>
    </div>
</body>
</html>