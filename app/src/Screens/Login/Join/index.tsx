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

// 스타일 설정
const BannerContainer = Styled.View`height:60px`

/*
 * Join 회원가입 스크린
 */
const Join = () => {
    const navigation = useNavigation();

    const {setUserInfo} = useContext(UserContext);
    const {webViewUrl, webViewSendMessage, confirmAlert} = useContext(ConfigContext);

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

        // 회원가입 성공
        } else if (key === 'joinSuccess') {
            navigation.navigate('Login')
        }
    };

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/login/join'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (joinWebview = ref)}
                onLoadEnd={e => webViewLoad(joinWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default Join;