import React from 'react'
import { createRoot } from 'react-dom/client'
import { Provider as ReduxProvider } from 'react-redux'
import { AuthProvider } from './contexts/AuthContext'
import App from './App'
import store from './store'
import 'core-js'
import './scss/style.scss'
import './scss/examples.scss'

const container = document.getElementById('root')
const root = createRoot(container)

root.render(
  <React.StrictMode>
    <ReduxProvider store={store}>
      <AuthProvider>
        <App />
      </AuthProvider>
    </ReduxProvider>
  </React.StrictMode>
)
