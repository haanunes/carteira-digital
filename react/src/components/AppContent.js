import React, { Suspense } from 'react'
import { Navigate, Route, Routes } from 'react-router-dom'
import { CContainer, CSpinner } from '@coreui/react'

import routes from '../routes'

const AppContent = () => {
  return (
    <CContainer className="px-4" lg>
      <Suspense fallback={<CSpinner color="primary" />}>
      <Routes>
        {routes.map((r, idx) => (
          <Route
            key={idx}
            path={r.path}
            element={r.element}
          />
        ))}
        {/* fallback 404 */}
        <Route path="*" element={<Navigate to="/404" />} />
      </Routes>
      </Suspense>
    </CContainer>
  )
}

export default React.memo(AppContent)
