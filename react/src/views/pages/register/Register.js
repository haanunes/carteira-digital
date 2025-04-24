import React, { useState, useContext } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import {
  CButton,
  CCard,
  CCardBody,
  CCardGroup,
  CCol,
  CContainer,
  CForm,
  CFormInput,
  CInputGroup,
  CInputGroupText,
  CRow,
} from '@coreui/react'
import CIcon from '@coreui/icons-react'
import { cilLockLocked, cilUser, cilEnvelopeClosed } from '@coreui/icons'
import api from '../../../services/api'

const Register = () => {
  const navigate = useNavigate()
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirm, setPasswordConfirm] = useState('')
  const [errors, setErrors] = useState({})
  const [isSubmitting, setIsSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    if (password !== passwordConfirm) {
      setErrors({ passwordConfirm: ['As senhas não coincidem.'] })
      return
    }
    setIsSubmitting(true)
    try {
      await api.post('/register', { name, email, password })
      navigate('/login')
    } catch (err) {
      if (err.response && err.response.status === 422) {
        setErrors(err.response.data.errors || { general: [err.response.data.message] })
      } else {
        setErrors({ general: ['Erro ao criar conta. Tente novamente mais tarde.'] })
      }
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <div className="bg-light min-vh-100 d-flex flex-row align-items-center">
      <CContainer>
        <CRow className="justify-content-center">
          <CCol md={8}>
            <CCardGroup className="shadow-sm">

              <CCard className="p-4">
                <CCardBody>
                  <CForm onSubmit={handleSubmit}>
                    <h1 className="mb-4">Cadastrar-se</h1>
                    <p className="text-muted mb-4">Crie sua conta</p>

                    {errors.general && (
                      <div className="alert alert-danger">
                        {errors.general.map((msg, i) => <div key={i}>{msg}</div>)}
                      </div>
                    )}

                    <CInputGroup className="mb-3">
                      <CInputGroupText>
                        <CIcon icon={cilUser} />
                      </CInputGroupText>
                      <CFormInput
                        type="text"
                        placeholder="Nome completo"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        disabled={isSubmitting}
                        required
                      />
                    </CInputGroup>
                    {errors.name && <div className="text-danger mb-3">{errors.name[0]}</div>}

                    <CInputGroup className="mb-3">
                      <CInputGroupText>
                        <CIcon icon={cilEnvelopeClosed} />
                      </CInputGroupText>
                      <CFormInput
                        type="email"
                        placeholder="E-mail"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        disabled={isSubmitting}
                        required
                      />
                    </CInputGroup>
                    {errors.email && <div className="text-danger mb-3">{errors.email[0]}</div>}

                    <CInputGroup className="mb-3">
                      <CInputGroupText>
                        <CIcon icon={cilLockLocked} />
                      </CInputGroupText>
                      <CFormInput
                        type="password"
                        placeholder="Senha"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        disabled={isSubmitting}
                        required
                      />
                    </CInputGroup>
                    {errors.password && <div className="text-danger mb-3">{errors.password[0]}</div>}

                    <CInputGroup className="mb-4">
                      <CInputGroupText>
                        <CIcon icon={cilLockLocked} />
                      </CInputGroupText>
                      <CFormInput
                        type="password"
                        placeholder="Confirme a senha"
                        value={passwordConfirm}
                        onChange={(e) => setPasswordConfirm(e.target.value)}
                        disabled={isSubmitting}
                        required
                      />
                    </CInputGroup>
                    {errors.passwordConfirm && <div className="text-danger mb-3">{errors.passwordConfirm[0]}</div>}

                    <CRow>
                      <CCol xs={12}>
                        <CButton
                          color="success"
                          className="w-100"
                          type="submit"
                          disabled={isSubmitting}
                        >
                          {isSubmitting ? 'Cadastrando...' : 'Criar conta'}
                        </CButton>
                      </CCol>
                    </CRow>

                  </CForm>
                </CCardBody>
              </CCard>

              <CCard
                className="text-white bg-primary text-center py-5"
                style={{ width: '44%' }}
              >
                <CCardBody className="d-flex flex-column justify-content-center">
                  <h2 className="mb-3">Já tem uma conta?</h2>
                  <p className="mb-4">Clique abaixo para acessar sua conta.</p>
                  <Link to="/login">
                    <CButton color="light" shape="rounded-pill" disabled={isSubmitting}>
                      Entrar
                    </CButton>
                  </Link>
                </CCardBody>
              </CCard>

            </CCardGroup>
          </CCol>
        </CRow>
      </CContainer>
    </div>
  )
}

export default Register
