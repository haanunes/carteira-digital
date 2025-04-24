import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  CRow, CCol, CCard, CCardHeader, CCardBody,
  CForm, CInputGroup, CInputGroupText, CFormInput,
  CButton, CSpinner, CAlert,
} from '@coreui/react'
import CIcon from '@coreui/icons-react'
import { cilUser, cilDollar } from '@coreui/icons'
import api from '../../../services/api'

const Transfer = () => {
  const navigate = useNavigate()
  const [balance, setBalance] = useState('0,00')
  const [payeeId, setPayeeId] = useState('')
  const [payeeInfo, setPayeeInfo] = useState(null)
  const [loadingPayee, setLoadingPayee] = useState(false)
  const [amount, setAmount] = useState('')
  const [errors, setErrors] = useState({})
  const [errorMsg, setErrorMsg] = useState('')
  const [successMsg, setSuccessMsg] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)

  useEffect(() => {
    api.get('/user')
      .then(res => {
        setBalance(res.data.data.wallet.balance)
      })
      .catch(() => {
      })
  }, [])

  useEffect(() => {
    if (!payeeId) {
      setPayeeInfo(null)
      return
    }
    setLoadingPayee(true)
    api.get(`/users/${payeeId}`)
      .then(res => {
        setPayeeInfo(res.data.data)  
        setErrors(prev => ({ ...prev, payee_id: null }))
      })
      .catch(err => {
        setPayeeInfo(null)
        setErrors(prev => ({
          ...prev,
          payee_id: err.response?.status === 404
            ? 'Usuário não encontrado'
            : 'Erro ao buscar usuário',
        }))
      })
      .finally(() => setLoadingPayee(false))
  }, [payeeId])

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setErrorMsg('')
    setSuccessMsg('')

    
    const newErrors = {}
    if (!payeeId) newErrors.payee_id = 'ID do recebedor é obrigatório'
    if (!amount) newErrors.amount = 'Valor é obrigatório'
    else if (isNaN(amount) || Number(amount) <= 0) newErrors.amount = 'Valor deve ser > 0'
    if (Object.keys(newErrors).length) {
      setErrors(newErrors)
      return
    }

    setIsSubmitting(true)
    try {
      await api.post('/transfer', {
        payee_id: payeeId,
        amount: Number(amount),
      })
      setSuccessMsg('Transferência realizada com sucesso')
      setTimeout(() => navigate('/transactions'), 1500)
    } catch (err) {
      if (err.response) {
        if (err.response.status === 422 && err.response.data.errors) {
          setErrors(err.response.data.errors)
        } else {
          setErrorMsg(err.response.data.message || 'Erro na transferência')
        }
      } else {
        setErrorMsg('Falha de conexão')
      }
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <CRow className="justify-content-center">
      <CCol md={6}>
        <CCard>
          {/* --- Mostrar saldo no topo --- */}
          <CCardHeader className="bg-primary text-white">
            <div className="d-flex justify-content-between align-items-center">
              <span>Saldo disponível</span>
              <span className="h5 mb-0">R$ {balance}</span>
            </div>
          </CCardHeader>

          <CCardBody>
            {(successMsg || errorMsg) && (
              <CAlert color={successMsg ? 'success' : 'danger'}>
                {successMsg || errorMsg}
              </CAlert>
            )}

            <CForm onSubmit={handleSubmit}>

              {/* --- Campo ID do recebedor + loader + nome --- */}
              <CInputGroup className="mb-3">
                <CInputGroupText>
                  <CIcon icon={cilUser} />
                </CInputGroupText>
                <CFormInput
                  type="number"
                  placeholder="ID do recebedor"
                  value={payeeId}
                  onChange={e => setPayeeId(e.target.value)}
                  disabled={isSubmitting}
                  required
                />
                {loadingPayee && <CSpinner size="sm" className="ms-2" />}
              </CInputGroup>
              {errors.payee_id && (
                <div className="text-danger mb-2">{errors.payee_id}</div>
              )}
              {payeeInfo && (
                <div className="mb-3">
                  <strong>Nome:</strong> {payeeInfo.name}<br/>
                </div>
              )}

              {/* --- Campo Valor --- */}
              <CInputGroup className="mb-4">
                <CInputGroupText>
                  <CIcon icon={cilDollar} />
                </CInputGroupText>
                <CFormInput
                  type="number"
                  step="0.01"
                  placeholder="Valor (ex: 100.00)"
                  value={amount}
                  onChange={e => setAmount(e.target.value)}
                  disabled={isSubmitting}
                  required
                />
              </CInputGroup>
              {errors.amount && (
                <div className="text-danger mb-3">{errors.amount}</div>
              )}

              {/* --- Botão Enviar --- */}
              <div className="d-grid">
                <CButton color="primary" type="submit" disabled={isSubmitting}>
                  {isSubmitting
                    ? <>
                        <CSpinner size="sm" className="me-2" />
                        Enviando...
                      </>
                    : 'Transferir'
                  }
                </CButton>
              </div>

            </CForm>
          </CCardBody>
        </CCard>
      </CCol>
    </CRow>
  )
}

export default Transfer
