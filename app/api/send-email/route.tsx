import nodemailer from 'nodemailer'

export async function POST(request) {
  try {
    const body = await request.json()
    const { to, subject, html, text } = body

    if (!to || !subject) {
      return Response.json({ error: 'Missing required fields: to, subject' }, { status: 400 })
    }

    const transporter = nodemailer.createTransport({
      host: process.env.MAIL_HOST,
      port: parseInt(process.env.MAIL_PORT || '587'),
      secure: false,
      auth: {
        user: process.env.MAIL_USERNAME,
        pass: process.env.MAIL_PASSWORD,
      },
    })

    await transporter.sendMail({
      from: `"${process.env.MAIL_FROM_NAME}" <${process.env.MAIL_FROM_ADDRESS}>`,
      to,
      subject,
      html: html || `<p>${text || ''}</p>`,
      text: text || '',
    })

    return Response.json({ success: true, message: 'Email sent successfully.' })
  } catch (err) {
    console.error('Email send error:', err)
    return Response.json({ success: false, message: err.message }, { status: 500 })
  }
}
