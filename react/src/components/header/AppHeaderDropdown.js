import React, { useContext } from 'react'
import { useNavigate, NavLink } from 'react-router-dom'
import {
  CDropdown,
  CDropdownItem,
  CDropdownMenu,
  CDropdownToggle,
  CDropdownHeader,
  CDropdownDivider,
} from '@coreui/react'
import CIcon from '@coreui/icons-react'
import { cilUser, cilPowerStandby, cilHistory } from '@coreui/icons'
import { AuthContext } from '../../contexts/AuthContext'
import api from '../../services/api' 

const AppHeaderDropdown = () => {
  const { user, logout } = useContext(AuthContext)
  const navigate = useNavigate()

  const handleLogout = async () => {
    try {
      await api.post('/logout')
    } catch {
    }
    logout()
    navigate('/login', { replace: true })
  }

  return (
    <CDropdown variant="nav-item">
      <CDropdownToggle className="py-0 pe-0" caret={false}>
        <CIcon icon={cilUser} size="xl" />
      </CDropdownToggle>
      <CDropdownMenu placement="bottom-end" className="pt-0">
        <CDropdownHeader className="bg-light fw-semibold">
          {user.name.toUpperCase()}
        </CDropdownHeader>
        <CDropdownItem
          as="button"
          onClick={handleLogout}
          className="text-danger"
        >
          <CIcon icon={cilPowerStandby} className="me-2" />
          Sair
        </CDropdownItem>
      </CDropdownMenu>
    </CDropdown>
  )
}

export default AppHeaderDropdown