import { createBrowserRouter } from 'react-router-dom';
import { RequireAdmin, RequireAuth } from './guards';
import { AdminLayout, CustomerLayout, PublicLayout } from './layouts';
import Home from '../pages/Home';
import { NotFound } from '../pages/NotFound';
import { PlaceholderPage } from '../pages/PlaceholderPage';

/**
 * Centralized route table. Feature routes render an honest placeholder
 * until their commits land — no fake booking, payment, or admin data.
 */
export const router = createBrowserRouter([
  {
    element: <PublicLayout />,
    children: [
      { index: true, element: <Home /> },
      {
        path: 'cabin/:propertySlug/:cabinSlug',
        element: <PlaceholderPage title="Cabin Detail" />,
      },
      { path: 'booking', element: <PlaceholderPage title="Booking" /> },
      { path: 'checkout', element: <PlaceholderPage title="Checkout" /> },
      { path: 'payment', element: <PlaceholderPage title="Payment" /> },
      {
        path: 'booking/success',
        element: <PlaceholderPage title="Booking Success" />,
      },
      {
        path: 'booking/failed',
        element: <PlaceholderPage title="Booking Failed" />,
      },
      { path: 'faq', element: <PlaceholderPage title="FAQ" /> },
      { path: 'location', element: <PlaceholderPage title="Location" /> },
      {
        path: 'house-rules',
        element: <PlaceholderPage title="House Rules" />,
      },
      { path: 'contact', element: <PlaceholderPage title="Contact" /> },
      { path: 'login', element: <PlaceholderPage title="Login" /> },
      { path: 'register', element: <PlaceholderPage title="Register" /> },
    ],
  },
  {
    path: 'account',
    element: <RequireAuth />,
    children: [
      {
        element: <CustomerLayout />,
        children: [
          { index: true, element: <PlaceholderPage title="Account" /> },
          {
            path: 'bookings',
            element: <PlaceholderPage title="My Bookings" />,
          },
          {
            path: 'bookings/:bookingCode',
            element: <PlaceholderPage title="Booking Detail" />,
          },
          {
            path: 'profile',
            element: <PlaceholderPage title="Profile" />,
          },
        ],
      },
    ],
  },
  {
    path: 'admin',
    element: <RequireAdmin />,
    children: [
      {
        element: <AdminLayout />,
        children: [
          { index: true, element: <PlaceholderPage title="Admin Dashboard" /> },
          {
            path: 'reservations',
            element: <PlaceholderPage title="Reservations" />,
          },
          {
            path: 'reservations/:reservationId',
            element: <PlaceholderPage title="Reservation Detail" />,
          },
          {
            path: 'availability',
            element: <PlaceholderPage title="Availability" />,
          },
          {
            path: 'customers',
            element: <PlaceholderPage title="Customers" />,
          },
          {
            path: 'customers/:userId',
            element: <PlaceholderPage title="Customer Detail" />,
          },
        ],
      },
    ],
  },
  { path: '*', element: <NotFound /> },
]);
