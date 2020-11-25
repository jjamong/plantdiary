import React, {useContext, useEffect, useState} from 'react';
import Styled from 'styled-components/native';
import {useNavigation, useRoute} from '@react-navigation/native';
import {WebView} from 'react-native-webview'

// 컨텍스트
import {ConfigContext} from '~/Context/Config';
import {UserContext} from '~/Context/User';

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';
import Loading from '~/Components/Loading';

// 스타일
const BannerContainer = Styled.View`flex: 0.1;`

/*
 * Calendar 캘린더 스크린
 */
const Login = () => {
    const navigation = useNavigation();
    const router = useRoute();

    const [spinner, setSpinner] = useState(false);

    const {webViewUrl} = useContext(ConfigContext);
    const {setUserInfo} = useContext(UserContext);

    useEffect(() => {
    }, []);

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = (): void => {
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {
            // 스피너 감추기
            hideSpinner();

        // 로그인 성공
        } else if (key === 'loginOK') {
            setUserInfo(data);
            navigation.navigate(router.params.loginNextScreen);

        // 회원가입 스크린 이동
        } else if (key === 'moveJoin') {
            navigation.navigate('Join');
        }
    };

    // 스피너 노출
    const showSpinner = (): void => {setSpinner(true)};
    // 스피너 감추기
    const hideSpinner = (): void => {setSpinner(false)};

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/login'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (loginWebview = ref)}
                onLoadStart={e => showSpinner()}
                onLoadEnd={e => webViewLoad()}
            />
            {spinner ? <Loading /> : null}
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default Login;