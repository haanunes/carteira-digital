import React, { useState, useEffect } from 'react'
import {
  CCard,
  CCardBody,
  CCardHeader,
  CRow,
  CCol,
  CSpinner,
  CTable,
  CTableHead,
  CTableBody,
  CTableRow,
  CTableHeaderCell,
  CTableDataCell,
  CAlert,
  CButton,
} from '@coreui/react'
import { FaEye, FaEyeSlash } from 'react-icons/fa'
import api from '../../../services/api'

const Transactions = () => {
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [transactions, setTransactions] = useState([])
  const [balance, setBalance] = useState('0,00')
  const [userId, setUserId] = useState(null)
  const [showValues, setShowValues] = useState(true)
  const [reversingId, setReversingId] = useState(null)

  const fetchData = async () => {
    setError('')
    try {
      const [resUser, resTx] = await Promise.all([
        api.get('/user'),
        api.get('/transactions'),
      ])
      setBalance(resUser.data.data.wallet.balance)
      setUserId(resUser.data.data.id)
      setTransactions(resTx.data.data)
    } catch {
      setError('Não foi possível carregar dados. Tente novamente.')
    } finally {
      setLoading(false)
      setReversingId(null)
    }
  }

  useEffect(() => {
    fetchData()
  }, [])

  const handleReverse = async (txId) => {
    if (!window.confirm('Deseja realmente reverter esta transação?')) return
    setReversingId(txId)
    try {
      await api.post(`/reverse/${txId}`)
      await fetchData()
    } catch (err) {
      alert(err.response?.data?.message || 'Erro ao reverter')
      setReversingId(null)
    }
  }

  if (loading) {
    return (
      <div className="vh-100 d-flex justify-content-center align-items-center">
        <CSpinner />
      </div>
    )
  }

  return (
    <CRow className="mb-4">
      {/* Card de Saldo */}
      <CCol xs={12}>
        <CCard className="border-primary">
          <CCardHeader className="d-flex justify-content-between bg-primary text-white">
            <span>Saldo Atual</span>
            <CButton
              color="transparent"
              size="sm"
              onClick={() => setShowValues((v) => !v)}
              style={{ color: 'white' }}
            >
              {showValues ? <FaEyeSlash /> : <FaEye />}
            </CButton>
          </CCardHeader>
          <CCardBody className="text-center">
            <span className="h2">
              {showValues ? `R$ ${balance}` : '••••••'}
            </span>
          </CCardBody>
        </CCard>
      </CCol>

      {/* Tabela de Transações */}
      <CCol xs={12} className="mt-4">
        {error && <CAlert color="danger">{error}</CAlert>}

        {!error && (
          <CTable responsive bordered>
            <CTableHead color="light">
              <CTableRow>
                <CTableHeaderCell>Data</CTableHeaderCell>
                <CTableHeaderCell>Tipo</CTableHeaderCell>
                <CTableHeaderCell className="text-end">Valor</CTableHeaderCell>
                <CTableHeaderCell>Status</CTableHeaderCell>
                <CTableHeaderCell className="text-center">Ações</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              {transactions.map((tx) => {
                const isOutgoing = tx.payer_id === userId
                const sign = isOutgoing ? '-' : ''
                const colorClass = isOutgoing ? 'text-danger' : 'text-success'

                return (
                  <CTableRow key={tx.id}>
                    <CTableDataCell>
                      {new Date(tx.created_at).toLocaleString('pt-BR')}
                    </CTableDataCell>
                    <CTableDataCell className="text-capitalize">
                      {tx.type}
                    </CTableDataCell>
                    <CTableDataCell className="text-end">
                      {showValues ? (
                        <span className={colorClass}>
                          {sign}R$ {tx.amount}
                        </span>
                      ) : (
                        '••••••'
                      )}
                    </CTableDataCell>
                    <CTableDataCell className="text-capitalize">
                      {tx.status}
                    </CTableDataCell>
                    <CTableDataCell className="text-center">
                      {tx.status === 'Concluído' ? (
                        <CButton
                          color="warning"
                          size="sm"
                          disabled={reversingId === tx.id}
                          onClick={() => handleReverse(tx.id)}
                        >
                          {reversingId === tx.id ? (
                            <CSpinner size="sm" />
                          ) : (
                            'Reverter'
                          )}
                        </CButton>
                      ) : (
                        <span className="text-muted">–</span>
                      )}
                    </CTableDataCell>
                  </CTableRow>
                )
              })}
            </CTableBody>
          </CTable>
        )}
      </CCol>
    </CRow>
  )
}

export default Transactions
