import React, { useContext } from 'react'
import { Navigate, Outlet } from 'react-router-dom'
import { AuthContext } from '../contexts/AuthContext'
import { CSpinner } from '@coreui/react'

export default function PrivateRoute() {
  const { user, loading } = useContext(AuthContext)

  if (loading) {
    return (
      <div className="vh-100 d-flex justify-content-center align-items-center">
        <CSpinner color="primary" />
      </div>
    )
  }

  if (!user) {
    return <Navigate to="/login" />
  }

  return <Outlet />
}
