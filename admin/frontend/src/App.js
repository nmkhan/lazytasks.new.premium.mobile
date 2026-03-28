// src/index.js
import React, {useEffect} from 'react';
import { render } from '@wordpress/element';
import { MantineProvider, createTheme } from '@mantine/core';
import '@mantine/core/styles.css';
import {ModalsProvider} from "@mantine/modals";
import { Notifications } from '@mantine/notifications';
import '@mantine/notifications/styles.css';
import {MobileApp} from "./components/qrCode/MobileApp";
import {Provider} from "react-redux";
import store, {premiumPersistor} from './store';
import LicenseNav from "./components/license/LicenseNav";
import {premiumRoutes} from "./configs/routes";

const theme = createTheme({
    colorScheme: 'light',
    primaryColor: 'blue',
    errorColor: 'red',
    fontFamily: 'Open Sans, sans-serif',
    cursorType: 'pointer',
    headings: {
        fontFamily: 'Open Sans, sans-serif',
    },
    legend: {
        fontFamily: 'Open Sans, sans-serif',
        fontSize: '16px',
    },

})

const App = () => {

    const mobileAppQRCode = () => {

        /*render(
            <Provider store={store}>
                <MantineProvider theme={theme}>
                    <Notifications />
                    <ModalsProvider>
                        <MobileApp />
                    </ModalsProvider>
                </MantineProvider>
            </Provider>,
            document.getElementById('lazytask_premium_mobile_app_qr_code')
        );*/
        
        render(
            <Provider store={store}>
                <MantineProvider>
                    <Notifications />
                    <MobileApp />
                </MantineProvider>
            </Provider>,
            document.getElementById('lazytask_premium_mobile_app_qr_code')
        );
    }
    const licenseTabButton = () => {

        render(
            <Provider store={store}>
                <MantineProvider theme={theme}>
                    <Notifications />
                    {/*<ModalsProvider>*/}
                        <LicenseNav />
                    {/*</ModalsProvider>*/}
                </MantineProvider>
            </Provider>,
            document.getElementById('lazytask_premium_license_tab_button')
        );
    }


    useEffect(() => {
        window.lazytaskPremium = {
            mobileAppQRCode,
            licenseTabButton,
            premiumAppRoutes: premiumRoutes,
        }
    }, []);

    return false;
}

export default App;

