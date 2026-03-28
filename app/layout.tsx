import '../sass/globals.scss'
import { Toaster } from 'react-hot-toast'

export const metadata = {
  title: 'DentaQueue',
  description: 'Dental Clinic Queue & Appointment Management System',
}

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>
        <Toaster position="top-right" />
        {children}
      </body>
    </html>
  )
}
