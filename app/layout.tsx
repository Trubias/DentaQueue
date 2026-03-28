import '../sass/globals.scss'
import { Toaster } from 'react-hot-toast'

export const metadata = {
  title: 'DentaQueue',
  description: 'Dental Clinic Queue Management System',
  icons: {
    icon: '/favicon.svg',
    shortcut: '/favicon.svg',
    apple: '/favicon.svg',
  },
}

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <head>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
      </head>
      <body>
        <Toaster position="top-right" />
        {children}
      </body>
    </html>
  )
}
