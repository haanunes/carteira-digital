import React from 'react'
import Deposit from './views/pages/transactions/Deposit'
import Transactions from './views/pages/transactions/Transactions'
import Transfer from './views/pages/transactions/Transfer'

const Dashboard = React.lazy(() => import('./views/dashboard/Dashboard'))
const Colors = React.lazy(() => import('./views/theme/colors/Colors'))
const Typography = React.lazy(() => import('./views/theme/typography/Typography'))

const routes = [
  { path: '/',           element: <Dashboard /> },    
  { path: '/dashboard',  element: <Dashboard /> },
  { path: '/deposit',    element: <Deposit /> },
  { path: '/transfer',   element: <Transfer /> },
  { path: '/transactions',element: <Transactions /> },
 
]

export default routes
