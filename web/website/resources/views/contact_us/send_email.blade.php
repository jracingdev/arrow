<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f6fa;padding:20px 0;font-family:Arial,Helvetica,sans-serif;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e0e0e0;">

        <!-- Header -->
        <tr>
          <td style="background:#4a6cf7;color:#ffffff;padding:18px 24px;font-size:20px;font-weight:bold;">
            {{ trans('lang.contact_email_subject') }}
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:24px;color:#333333;font-size:14px;line-height:1.6;">

            <p>{{ trans('lang.contact_email_intro') }}</p>

            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;margin-top:10px;">
              <tr>
                <td style="font-weight:bold;width:140px;border-bottom:1px solid #eee;">
                  {{ trans('lang.name') }}
                </td>
                <td style="border-bottom:1px solid #eee;">
                  {{ $data['name'] }}
                </td>
              </tr>

              <tr>
                <td style="font-weight:bold;border-bottom:1px solid #eee;">
                  {{ trans('lang.email') }}
                </td>
                <td style="border-bottom:1px solid #eee;">
                  {{ $data['email'] }}
                </td>
              </tr>

              <tr>
                <td style="font-weight:bold;border-bottom:1px solid #eee;">
                  {{ trans('lang.phone') }}
                </td>
                <td style="border-bottom:1px solid #eee;">
                  {{ $data['phone'] }}
                </td>
              </tr>
            </table>

            <!-- Message -->
            <div style="margin-top:20px;">
              <strong>{{ trans('lang.lbl_message') }}</strong>
              <div style="margin-top:8px;padding:12px;background:#f8f9fc;border-radius:6px;border:1px solid #eee;">
                {{ $data['message'] }}
              </div>
            </div>

          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
