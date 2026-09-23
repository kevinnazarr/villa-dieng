import { createBrowserRouter } from 'react-router-dom';
import { RequireAdmin, RequireAuth } from './guards';
import { AdminLayout, CustomerLayout, PublicLayout } from './layouts';
import Home from '../pages/Home';
import { Login } from '../pages/Login';
import { Register } from '../pages/Register';
import { Forbidden } from '../pages/Forbidden';
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
        element: <PlaceholderPage titleKey="page.cabinDetail" />,
      },
      { path: 'booking', element: <PlaceholderPage titleKey="page.booking" /> },
      { path: 'checkout', element: <PlaceholderPage titleKey="page.checkout" /> },
      { path: 'payment', element: <PlaceholderPage titleKey="page.payment" /> },
      {
        path: 'booking/success',
        element: <PlaceholderPage titleKey="page.bookingSuccess" />,
      },
      {
        path: 'booking/failed',
        element: <PlaceholderPage titleKey="page.bookingFailed" />,
      },
      { path: 'faq', element: <PlaceholderPage titleKey="page.faq" /> },
      { path: 'location', element: <PlaceholderPage titleKey="page.location" /> },
      {
        path: 'house-rules',
        element: <PlaceholderPage titleKey="page.houseRules" />,
      },
      { path: 'contact', element: <PlaceholderPage titleKey="page.contact" /> },
      { path: 'login', element: <Login /> },
      { path: 'register', element: <Register /> },
      { path: 'forbidden', element: <Forbidden /> },
    ],
  },
  {
    path: 'account',
    element: <RequireAuth />,
    children: [
      {
        element: <CustomerLayout />,
        children: [
          { index: true, element: <PlaceholderPage titleKey="page.account" /> },
          {
            path: 'bookings',
            element: <PlaceholderPage titleKey="page.myBookings" />,
          },
          {
            path: 'bookings/:bookingCode',
            element: <PlaceholderPage titleKey="page.bookingDetail" />,
          },
          {
            path: 'profile',
            element: <PlaceholderPage titleKey="page.profile" />,
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
          { index: true, element: <PlaceholderPage titleKey="page.adminDashboard" /> },
          {
            path: 'reservations',
            element: <PlaceholderPage titleKey="page.reservations" />,
          },
          {
            path: 'reservations/:reservationId',
            element: <PlaceholderPage titleKey="page.reservationDetail" />,
          },
          {
            path: 'availability',
            element: <PlaceholderPage titleKey="page.availability" />,
          },
          {
            path: 'customers',
            element: <PlaceholderPage titleKey="page.customers" />,
          },
          {
            path: 'customers/:userId',
            element: <PlaceholderPage titleKey="page.customerDetail" />,
          },
        ],
      },
    ],
  },
  { path: '*', element: <NotFound /> },
]);
