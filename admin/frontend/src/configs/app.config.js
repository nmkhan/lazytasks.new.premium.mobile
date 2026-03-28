const appConfig = {
    apiPrefix: '/api',
    authenticatedEntryPath: '/dashboard',
    unAuthenticatedEntryPath: '/lazy-login',
    tourPath: '/',
    locale: 'en',
    enableMock: false,
    liveApiUrl: `${appLocalizerPremium?.apiUrl}/lazytasks/api/v3`,
    liveSiteUrl: `${appLocalizerPremium?.homeUrl}`,
    localApiUrl: 'http://localhost:9000',
}

export default appConfig
