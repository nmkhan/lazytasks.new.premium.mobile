import React, { Fragment, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
    Button,
    Group,
    TextInput,
    Grid,
    ScrollArea,
    Text,
    Flex,
    Popover,
    LoadingOverlay,
    Title,
    Card, Image, Stack, Box, Anchor
} from '@mantine/core';

import { useForm } from "@mantine/form";
import {
    fetchLicenseKey,
    licenseRemove,
    licenseValidation,
    removeErrorMessage,
    removeSuccessMessage
} from "./store/licenseSlice";
import { showNotification } from "@mantine/notifications";
import appConfig from "../../configs/app.config";
import { IconCircleCheck, IconInfoCircle } from "@tabler/icons-react";
import { Tooltip } from "recharts";
import { useDisclosure } from "@mantine/hooks";
import lazytasksGuideLineThreeStep from '../../assets/lazytasks-guideline.webp';
import androidQR from '../../assets/android.png';
import iosQR from '../../assets/ios.png';
import appleStoreIcon from '../../assets/app-store-icon.png';
import googlePlayIcon from '../../assets/google-play-icon.png';
import lazytasksPriceSection from '../../assets/lazytasks-purchase-section-bg.png';

const LicenseForm = () => {
    const dispatch = useDispatch();

    const { licenseKey, isLicenseSuccess, isLicenseError } = useSelector((state) => state.license.license);
    const [license, setLicense] = useState(licenseKey || '');
    const [isSuccess, setIsSuccess] = useState(false);
    const [isVerified, setIsVerified] = useState(isLicenseSuccess || false);
    //isNotEmpty
    const [isEmpty, setIsEmpty] = useState(true);

    const form = useForm({
        mode: 'uncontrolled',
        initialValues: {
            license_key: license || '',
        },

        validate: {
            license_key: (value) => (value.length < 1 ? 'License key is required' : null),
        },
    });
    const handleSubmit = (values) => {
        values['domain'] = appConfig.liveSiteUrl;
        dispatch(licenseValidation(values)).then((response) => {
            if (response.payload && response.payload.status && response.payload.status === 200) {
                setIsSuccess(true);
                setIsVerified(true);
                setIsEmpty(false);
                setLicense(response.payload.data.license_key);
                showNotification({
                    id: 'load-data',
                    loading: true,
                    title: 'License',
                    message: response.payload && response.payload.message && response.payload.message,
                    autoClose: 2000,
                    disallowClose: true,
                    color: 'green',
                });
                dispatch(removeSuccessMessage());

                //setTimeout for isSuccess to false after 2 seconds
                setTimeout(() => {
                    setIsSuccess(false);
                }, 5000);
            }
            if (response.payload && response.payload.status && response.payload.status !== 200) {
                form.setFieldError('license_key', response.payload.message);
                dispatch(removeErrorMessage());
                setIsVerified(false);
                setIsEmpty(false);
            }

        });
    };
    const handleDeleteSubmit = (values) => {
        values['domain'] = appConfig.liveSiteUrl;
        dispatch(licenseRemove(values)).then((response) => {
            if (response.payload && response.payload.status && response.payload.status === 200) {
                setIsSuccess(true);
                setIsVerified(false);
                setIsEmpty(true);
                setLicense('');
                form.setFieldValue('license_key', '');

                showNotification({
                    id: 'load-data',
                    loading: true,
                    title: 'License',
                    message: response.payload && response.payload.message && response.payload.message,
                    autoClose: 2000,
                    disallowClose: true,
                    color: 'green',
                });
                dispatch(removeSuccessMessage());

                //setTimeout for isSuccess to false after 2 seconds
                setTimeout(() => {
                    setIsSuccess(false);
                }, 5000);
            }
            if (response.payload && response.payload.status && response.payload.status !== 200) {
                form.setFieldError('license_key', response.payload.message);
                dispatch(removeErrorMessage());
                setIsVerified(false);
                setIsEmpty(false);
            }

        });
    };

    useEffect(() => {
        setLicense(licenseKey);
        if (licenseKey) {
            form.setFieldValue('license_key', licenseKey);
        }
    }, [licenseKey]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        dispatch(fetchLicenseKey()).then((res) => {
            if (res.payload && res.payload.status && res.payload.status === 200) {
                setLicense(res.payload.data);
                setIsVerified(true);
                form.setFieldValue('license_key', res.payload.data);
            }
            setIsLoading(false);
        });
    }, []);
    //useEffect isLicenseSuccess
    useEffect(() => {
        setIsVerified(isLicenseSuccess)
        setIsEmpty(!isLicenseSuccess);
    }, [isLicenseSuccess]);

    //handleInputChange
    const handleInputChange = (event) => {
        const { value } = event.target;

        if (value.length < 1) {
            setIsEmpty(true)
            form.setFieldError('license_key', 'License key is required');
        } else {
            setIsEmpty(false)
            form.clearFieldError('license_key');
            setLicense(value);
        }
    };
    const [opened, { close, open }] = useDisclosure(false);
    const qrCode = window.appLocalizerPremium && window.appLocalizerPremium.qrCode;
    const remainingDays = appLocalizer.remainingDays || 0;

    return (
        <ScrollArea className="h-[calc(100vh-250px)] pb-[2px] overflow-x-" scrollbars="y" scrollbarSize={4}>
            <LoadingOverlay visible={isLoading} zIndex={1000} overlayProps={{ radius: "sm", blur: 2 }} />
            {!isLoading && !isVerified &&
                <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
                    <Grid justify="flex-start" align="flex-start">
                        <Grid.Col span={6} offset={3}>
                            <Card radius="md" bg="#F5F8F9">
                                <Title color="#39758d" order={4} className="text-center w-full pb-2">
                                    Activate Your Plugin License
                                </Title>
                                <Grid>
                                    <Grid.Col span={9}>
                                        <TextInput
                                            className="w-full"
                                            label={
                                                <>
                                                    <Group className={`pb-2`} spacing={5} gap={5}>
                                                        Enter your license key to activate the LazyTasks plugin.
                                                        <Popover width={320} position="top-start" withArrow shadow="md"
                                                            opened={opened}>
                                                            <Popover.Target>
                                                                <IconInfoCircle onMouseEnter={open} onMouseLeave={close}
                                                                    size={16} />
                                                            </Popover.Target>
                                                            <Popover.Dropdown>
                                                                Insert your License key here which you will find to your
                                                                lazycoders.co site profile.
                                                            </Popover.Dropdown>
                                                        </Popover>
                                                    </Group>
                                                </>
                                            }
                                            placeholder="Enter your license key"
                                            key={form.key('license_key')}
                                            defaultValue={() => license || ''}
                                            {...form.getInputProps('license_key')}
                                            onChange={(event) => {
                                                form.getInputProps('license_key').onChange(event); // Let Mantine handle form state
                                                handleInputChange(event);   // Your custom logic
                                            }}
                                        />
                                        {isSuccess &&
                                            <p style={{
                                                "color": "#39758d",
                                                "fontSize": 'var(--input-error-size, calc(var(--mantine-font-size-sm) - calc(0.125rem * var(--mantine-scale))))'
                                            }}
                                                className="!text-[10px] mantine-InputWrapper-success mantine-TextInput-success"
                                                id="mantine-89ny2ja26-success">License key verified successfully!</p>
                                        }

                                    </Grid.Col>
                                    <Grid.Col span={3}>
                                        <Button
                                            leftSection={isVerified && !isEmpty && <IconCircleCheck size={18} />}
                                            style={{
                                                marginTop: '38px',
                                                backgroundColor: isEmpty ? 'RGB(212, 217, 220)' : !isEmpty && !isVerified ? '#ED7D31' : isVerified ? '#39758d' : 'RGB(212, 217, 220)',
                                            }}
                                            variant="filled"
                                            color="#ED7D31"
                                            type="submit">
                                            {isEmpty ? 'Verify' : !isEmpty && !isVerified ? 'Verify' : 'Verified'}
                                        </Button>
                                    </Grid.Col>
                                </Grid>
                            </Card>
                        </Grid.Col>
                    </Grid>

                </form>
            }

            {!isLoading && isVerified &&
                <Fragment>

                    <form onSubmit={form.onSubmit((values) => handleDeleteSubmit(values))}>
                        <Grid justify="flex-start" align="flex-start">
                            <Grid.Col span={6} offset={3}>
                                <Card radius="md" bg="#F5F8F9">
                                    <Title color="#39758d" order={4} className="text-center w-full pb-2">
                                        Activate Your Plugin License
                                    </Title>
                                    <Grid>
                                        <Grid.Col span={9}>
                                            <TextInput
                                                className="w-full"
                                                label={
                                                    <>
                                                        <Group className={`pb-2`} spacing={5} gap={5}>
                                                            Enter your license key to activate the LazyTasks plugin.
                                                            <Popover width={320} position="top-start" withArrow
                                                                shadow="md"
                                                                opened={opened}>
                                                                <Popover.Target>
                                                                    <IconInfoCircle onMouseEnter={open}
                                                                        onMouseLeave={close} size={16} />
                                                                </Popover.Target>
                                                                <Popover.Dropdown>
                                                                    Insert your License key here which you will find to
                                                                    your
                                                                    lazycoders.co site profile.
                                                                </Popover.Dropdown>
                                                            </Popover>
                                                        </Group>
                                                    </>
                                                }
                                                placeholder="Enter your license key"
                                                defaultValue={license || ''}
                                                disabled
                                            />
                                        </Grid.Col>
                                        <Grid.Col span={3}>
                                            <Button
                                                leftSection={isVerified && !isEmpty && <IconCircleCheck size={18} />}
                                                style={{
                                                    marginTop: '40px',
                                                    backgroundColor: '#ED7D31',
                                                }}
                                                variant="filled"
                                                color="#ED7D31"
                                                type="submit">
                                                Delete
                                            </Button>
                                        </Grid.Col>
                                    </Grid>
                                </Card>
                            </Grid.Col>
                        </Grid>

                    </form>
                </Fragment>
            }

            <Grid>
                {remainingDays >= 10 && appLocalizer.licenseStatus === '' && appLocalizer.premiumInstalled !== '' && window.appLocalizerPremium &&
                    <Grid.Col span={12} mb={0}>
                        <Card radius="md" bg="#F5F8F9" className="relative">
                            <Image
                                radius="md"
                                src={lazytasksPriceSection}
                            />
                            <Box className="absolute top-0 left-0 w-full h-full flex flex-col md:flex-row justify-between items-center p-4 md:p-8">

                                <Box className="md:w-1/2 flex justify-center">

                                </Box>
                                <Box className="md:w-1/2 text-center md:text-left mb-4 md:mb-0">
                                    <Title order={3} c="#000" className="font-bold mb-2">
                                        Your trial is about to expire
                                    </Title>
                                    <Text c="#000" className="mb-4">
                                        Upgrade your experience with the Lazytasks mobile App and get even more out of your Tasks Management!
                                    </Text>
                                    <Anchor href="https://lazycoders.co/lazytasks/" target="_blank" underline='never'>
                                        <Button
                                            variant="gradient"
                                            bg={`#ED7D31`}
                                            // gradient={{ from: '#ED7D31', to: 'yellow', deg: 45 }}
                                            size="sm"
                                            px="xl"
                                            mt={`sm`}
                                        // style={{height: '48px'}}
                                        >
                                            Purchase Now
                                        </Button>
                                    </Anchor>
                                </Box>
                            </Box>

                        </Card>
                    </Grid.Col>
                }
                <Title className={`text-center w-full pt-2`} order={4}>How to connect “LazyTasks” mobile app with installed plugin</Title>
                <Grid.Col span={12} mb="sm">
                    <Card radius="md" bg="#F5F8F9">
                        <Image
                            radius="md"
                            src={lazytasksGuideLineThreeStep}
                        />
                    </Card>
                </Grid.Col>

                <Grid.Col span={7} mb="sm">
                    <Grid>
                        <Grid.Col span={12}>
                            <Card radius="md" bg="#F5F8F9"
                                className="flex justify-center items-center p-4 w-full">
                                <Grid className={`w-full`}>
                                    <Grid.Col span={12} pb={0}>
                                        <Box className={`rounded-md`} bg="#fff" p={`sm`}>
                                            <Text size="md" fw={700} mb={0} c="#000" ta="center">
                                                <pill style={{
                                                    padding: '3px 8px 3px 15px',
                                                    backgroundColor: '#ED7D31',
                                                    borderRadius: '25px',
                                                    color: '#fff',
                                                    marginRight: '10px'
                                                }}>
                                                    Step-1:
                                                </pill>
                                                Download LazyTasks Mobile App
                                            </Text>
                                        </Box>
                                    </Grid.Col>
                                    <Grid.Col span={6} style={{ display: 'flex', justifyContent: 'center' }}>
                                        <Anchor href="https://play.google.com/store/apps/details?id=com.lazytasks.lazycoders" target="_blank">
                                            <Stack align="center" w="100%" bg="#fff" p="md" radius="lg" h={250}>

                                                <Image
                                                    radius="sm"
                                                    h={180}
                                                    w={180}
                                                    fit="contain"
                                                    src={androidQR}
                                                />
                                                <Image
                                                    radius="sm"
                                                    h={32}
                                                    w="auto"
                                                    src={googlePlayIcon}
                                                />
                                            </Stack>
                                        </Anchor>
                                    </Grid.Col>
                                    <Grid.Col span={6} style={{ display: 'flex', justifyContent: 'center' }}>
                                        <Anchor href="https://apps.apple.com/us/app/lazytasks/id6499516984" target="_blank">
                                            <Stack align="center" w="100%" bg="#fff" p="md" radius="md" h={250}>

                                                <Image
                                                    radius="sm"
                                                    h={180}
                                                    w={180}
                                                    fit="contain"
                                                    src={iosQR}
                                                />
                                                <Image
                                                    radius="sm"
                                                    h={32}
                                                    w="auto"
                                                    src={appleStoreIcon}
                                                />
                                            </Stack>
                                        </Anchor>
                                    </Grid.Col>
                                </Grid>

                            </Card>
                        </Grid.Col>

                    </Grid>
                </Grid.Col>

                <Grid.Col span={5} mb="sm">

                    <Card radius="md" bg="#F5F8F9"
                        className="flex flex-col justify-center items-end p-4">
                        <Grid>
                            <Grid.Col span={12} pb={0}>
                                <Box className={`rounded-md`} bg="#fff" p={`sm`}>
                                    <Text size="md" fw={700} mb={0} c="#000" ta="center">
                                        <pill style={{
                                            padding: '3px 8px 3px 15px',
                                            backgroundColor: '#ED7D31',
                                            borderRadius: '25px',
                                            color: '#fff',
                                            // fontSize: '14px',
                                            marginRight: '10px'
                                        }}>Step-2:
                                        </pill>
                                        Connect Your LazyTasks Mobile App
                                    </Text>
                                </Box>
                            </Grid.Col>
                            <Grid.Col span={12}>
                                <Stack justify="start" align="center" w="100%" bg="#fff" p="md" radius="lg" h={250}>
                                    <Image
                                        radius="sm"
                                        h={200}
                                        w={200}
                                        fit="contain"
                                        src={qrCode}
                                        style={{ marginTop: '-15px' }}
                                    />
                                </Stack>
                            </Grid.Col>
                        </Grid>


                    </Card>
                </Grid.Col>
            </Grid>

        </ScrollArea>
    );
}
export default LicenseForm;