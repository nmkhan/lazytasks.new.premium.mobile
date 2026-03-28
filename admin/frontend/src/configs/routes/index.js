import React from 'react'

export const premiumRoutes = [
    {
        key: 'license',
        path: '/license',
        component: React.lazy(() => import('../../components/license/License')),
        authority: [],
    }
]
