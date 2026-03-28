import React from "react";
import {Provider, useDispatch, useSelector} from "react-redux";
import {Button, Group, MantineProvider, ScrollArea, Text, TextInput, Grid, createTheme} from '@mantine/core';

import { ModalsProvider }  from "@mantine/modals";
import store from "../../store";
import LicenseForm from "./LicenseForm";

const License=() => {
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
    return (
        <Provider store={store}>
            <MantineProvider theme={theme}>
                <ModalsProvider>
                    <LicenseForm />
                </ModalsProvider>
            </MantineProvider>
        </Provider>

    );
}
export default License; 