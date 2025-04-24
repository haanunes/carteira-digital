import React, { useContext } from 'react'
import { useNavigate } from 'react-router-dom'
import CIcon from '@coreui/icons-react'
import {
  cilSpeedometer,
  cilDollar,
  cilSwapHorizontal,
  cilHistory,
  cilAccountLogout,
} from '@coreui/icons'
import { CNavItem, CNavTitle } from '@coreui/react'
import { AuthContext } from './contexts/AuthContext'
import api from './services/api'

const LogoutNavItem = (props) => {
  const { component, ...rest } = props

  const { setUser } = useContext(AuthContext)
  const navigate = useNavigate()

  const handleClick = async () => {
    try { await api.post('/logout') } catch {}
    localStorage.removeItem('token')
    setUser(null)
    navigate('/login', { replace: true })
  }

  return (
    <CNavItem
      {...rest}
      component="button"
      onClick={handleClick}
    />
  )
}

const _nav = [
  {
    component: CNavItem,
    name: 'Dashboard',
    to: '/dashboard',
    icon: <CIcon icon={cilSpeedometer} customClassName="nav-icon" />,
  },
  {
    component: CNavTitle,
    name: 'Operações',
  },
  {
    component: CNavItem,
    name: 'Depósito',
    to: '/deposit',
    icon: <CIcon icon={cilDollar} customClassName="nav-icon" />,
  },
  {
    component: CNavItem,
    name: 'Transferir',
    to: '/transfer',
    icon: <CIcon icon={cilSwapHorizontal} customClassName="nav-icon" />,
  },
  {
    component: CNavItem,
    name: 'Histórico',
    to: '/transactions',
    icon: <CIcon icon={cilHistory} customClassName="nav-icon" />,
  },

]

export default _nav
