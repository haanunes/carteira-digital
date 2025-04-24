import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  CContainer,
  CRow,
  CCol,
  CCard,
  CCardBody,
  CForm,
  CInputGroup,
  CInputGroupText,
  CFormInput,
  CButton,
  CSpinner,
  CAlert,
} from '@coreui/react'
import CIcon from '@coreui/icons-react'
import { cilDollar } from '@coreui/icons'
import api from '../../../services/api'

const Deposit = () => {
  const navigate = useNavigate()
  const [amount, setAmount] = useState('')
  const [errors, setErrors] = useState(null)
  const [success, setSuccess] = useState(null)
  const [isSubmitting, setIsSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors(null)
    setSuccess(null)
    const value = parseFloat(amount.replace(',', '.'))
    if (isNaN(value) || value <= 0) {
      setErrors(['Informe um valor de depósito válido maior que zero.'])
      return
    }
    setIsSubmitting(true)
    try {
      const res = await api.post('/deposit', { amount: value })
      setSuccess('Depósito realizado com sucesso!')
      setTimeout(() => navigate('/transactions'), 1500)
    } catch (err) {
      if (err.response && err.response.status === 422) {
        const apiErrors = err.response.data.errors
        setErrors(apiErrors.amount || [err.response.data.message])
      } else {
        setErrors(['Erro ao processar depósito. Tente novamente.'])
      }
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <CContainer className="mt-5">
      <CRow className="justify-content-center">
        <CCol md={6}>
          <CCard className="shadow-sm">
            <CCardBody>
              <h2 className="mb-4 text-center">Depósito</h2>

              {errors && (
                <CAlert color="danger">
                  {errors.map((msg, i) => (
                    <div key={i}>{msg}</div>
                  ))}
                </CAlert>
              )}

              {success && (
                <CAlert color="success">{success}</CAlert>
              )}

              <CForm onSubmit={handleSubmit}>
                <CInputGroup size="lg" className="mb-3">
                  <CInputGroupText>
                    <CIcon icon={cilDollar} />
                  </CInputGroupText>
                  <CFormInput
                    type="text"
                    placeholder="Valor (ex: 100.00)"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                    disabled={isSubmitting}
                    required
                  />
                </CInputGroup>

                <div className="d-grid">
                  <CButton color="primary" size="lg" type="submit" disabled={isSubmitting}>
                    {isSubmitting ? (
                      <>
                        <CSpinner size="sm" className="me-2" />
                        Enviando...
                      </>
                    ) : (
                      'Depositar'
                    )}
                  </CButton>
                </div>
              </CForm>
            </CCardBody>
          </CCard>
        </CCol>
      </CRow>
    </CContainer>
  )
}
export default Deposit
