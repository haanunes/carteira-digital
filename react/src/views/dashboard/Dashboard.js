import React, { useState, useEffect } from 'react'
import {
  CCard,
  CCardHeader,
  CCardBody,
  CRow,
  CCol,
  CSpinner,
} from '@coreui/react'
import { Line, Bar, Pie } from 'react-chartjs-2'
import {
  Chart as ChartJS,
  CategoryScale,
  TimeScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
import 'chartjs-adapter-date-fns'
import WidgetsDropdown from '../widgets/WidgetsDropdown'
import api from '../../services/api'

ChartJS.register(
  CategoryScale,
  TimeScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler,
)

const Dashboard = () => {
  const [loading, setLoading] = useState(true)
  const [userId, setUserId] = useState(null)
  const [balanceData, setBalanceData] = useState(null)
  const [depTransData, setDepTransData] = useState(null)
  const [typeDistData, setTypeDistData] = useState(null)
  const [cashFlowData, setCashFlowData] = useState(null)

  useEffect(() => {
    setLoading(true)
    Promise.all([api.get('/user'), api.get('/transactions')])
      .then(([resUser, resTx]) => {
        const uid = resUser.data.data.id
        setUserId(uid)
        buildCharts(resTx.data.data, uid)
      })
      .finally(() => setLoading(false))
  }, [])

  const buildCharts = (txs, uid) => {
    const typeKey = {
      Depósito: 'deposit',
      Transferência: 'transfer',
      Reversão: 'reversal',
    }

    const sorted = [...txs].sort(
      (a, b) => new Date(a.created_at) - new Date(b.created_at),
    )
    let bal = 0
    const balLabels = []
    const balValues = []

    sorted.forEach((tx) => {
      balLabels.push(tx.created_at)
      const amt = parseFloat(tx.amount.replace(',', '.'))
      const signed =
        tx.payee_id === uid
          ? amt
          : tx.payer_id === uid
          ? -amt
          : 0
      bal += signed
      balValues.push(parseFloat(bal.toFixed(2)))
    })

    setBalanceData({
      labels: balLabels,
      datasets: [
        {
          label: 'Saldo',
          data: balValues,
          borderColor: 'rgba(75,192,192,1)',
          backgroundColor: 'rgba(75,192,192,0.2)',
          fill: false,
        },
      ],
    })

    const monthly = {}
    sorted.forEach((tx) => {
      const m = tx.created_at.slice(0, 7)
      if (!monthly[m]) monthly[m] = { deposit: 0, transfer: 0 }
      const amt = parseFloat(tx.amount.replace(',', '.'))
      const key = typeKey[tx.type]
      const signed =
        tx.payee_id === uid
          ? amt
          : tx.payer_id === uid
          ? -amt
          : 0
      if (key === 'deposit') monthly[m].deposit += signed
      if (key === 'transfer') monthly[m].transfer += signed
    })
    const months = Object.keys(monthly).sort()
    setDepTransData({
      labels: months,
      datasets: [
        {
          label: 'Net Depósitos',
          data: months.map((m) =>
            parseFloat(monthly[m].deposit.toFixed(2)),
          ),
          backgroundColor: 'rgba(75,192,192,0.5)',
        },
        {
          label: 'Net Transferências',
          data: months.map((m) =>
            parseFloat(monthly[m].transfer.toFixed(2)),
          ),
          backgroundColor: 'rgba(255,99,132,0.5)',
        },
      ],
    })

    const counts = { deposit: 0, transfer: 0, reversal: 0 }
    txs.forEach((tx) => {
      const key = typeKey[tx.type]
      counts[key] = (counts[key] || 0) + 1
    })
    setTypeDistData({
      labels: ['Depósito', 'Transferência', 'Reversão'],
      datasets: [
        {
          data: [
            counts.deposit,
            counts.transfer,
            counts.reversal,
          ],
          backgroundColor: [
            '#4bc0c0',
            '#ff6384',
            '#36a2eb',
          ],
        },
      ],
    })

    const daily = {}
    sorted.forEach((tx) => {
      const d = tx.created_at.slice(0, 10)
      const amt = parseFloat(tx.amount.replace(',', '.'))
      const signed =
        tx.payee_id === uid
          ? amt
          : tx.payer_id === uid
          ? -amt
          : 0
      daily[d] = (daily[d] || 0) + signed
    })
    const days = Object.keys(daily).sort()
    setCashFlowData({
      labels: days,
      datasets: [
        {
          label: 'Fluxo de Caixa',
          data: days.map((d) =>
            parseFloat(daily[d].toFixed(2)),
          ),
          backgroundColor: 'rgba(54,162,235,0.4)',
          borderColor: 'rgba(54,162,235,1)',
          fill: true,
        },
      ],
    })
  }

  if (loading || !balanceData) {
    return (
      <div className="vh-100 d-flex justify-content-center align-items-center">
        <CSpinner />
      </div>
    )
  }

  return (
    <>
      <CRow className="mb-4">
        <CCol md={6}>
          <CCard>
            <CCardHeader>Saldo ao Longo do Tempo</CCardHeader>
            <CCardBody>
              <Line
                data={balanceData}
                options={{
                  scales: {
                    x: { type: 'time', time: { unit: 'day' } },
                    y: { beginAtZero: true },
                  },
                }}
              />
            </CCardBody>
          </CCard>
        </CCol>
        <CCol md={6}>
          <CCard>
            <CCardHeader>Depósitos vs Transferências (líquido)</CCardHeader>
            <CCardBody>
              <Bar
                data={depTransData}
                options={{
                  scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true },
                  },
                }}
              />
            </CCardBody>
          </CCard>
        </CCol>
      </CRow>

      <CRow className="mb-4">
        <CCol md={6}>
          <CCard>
            <CCardHeader>Distribuição de Operações</CCardHeader>
            <CCardBody>
              <Pie data={typeDistData} />
            </CCardBody>
          </CCard>
        </CCol>
        <CCol md={6}>
          <CCard>
            <CCardHeader>Fluxo de Caixa Diário</CCardHeader>
            <CCardBody>
              <Line
                data={cashFlowData}
                options={{
                  scales: { y: { beginAtZero: true } },
                  plugins: { filler: { propagate: true } },
                }}
              />
            </CCardBody>
          </CCard>
        </CCol>
      </CRow>
    </>
  )
}

export default Dashboard
