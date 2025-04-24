import React, { Suspense, useEffect } from 'react'
import { HashRouter, Route, Routes } from 'react-router-dom'
import { useSelector } from 'react-redux'
import { CSpinner, useColorModes } from '@coreui/react'
import './scss/style.scss'
import './scss/examples.scss'
import PrivateRoute from './routes/PrivateRoute'

const DefaultLayout = React.lazy(() => import('./layout/DefaultLayout'))
const Login       = React.lazy(() => import('./views/pages/login/Login'))
const Register    = React.lazy(() => import('./views/pages/register/Register'))
const Page404     = React.lazy(() => import('./views/pages/page404/Page404'))
const Page500     = React.lazy(() => import('./views/pages/page500/Page500'))

const App = () => {
  const { isColorModeSet, setColorMode } = useColorModes('coreui-free-react-admin-template-theme')
  const storedTheme = useSelector((state) => state.theme)

  useEffect(() => {
  }, [isColorModeSet, setColorMode, storedTheme])

  return (
    <HashRouter>
      <Suspense
        fallback={
          <div className="pt-3 text-center">
            <CSpinner color="primary" variant="grow" />
          </div>
        }
      >
        <Routes>
          {/* Rotas públicas */}
          <Route path="/login"    element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/404"      element={<Page404 />} />
          <Route path="/500"      element={<Page500 />} />

          {/* Rotas protegidas pelo PrivateRoute */}
          <Route element={<PrivateRoute />}>
            <Route path="/*" element={<DefaultLayout />} />
          </Route>
        </Routes>
      </Suspense>
    </HashRouter>
  )
}

export default App
