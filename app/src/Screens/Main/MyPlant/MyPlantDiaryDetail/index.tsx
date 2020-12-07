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
 * MyPlantDiaryDetail 다이어리 상세 스크린
 */
const MyPlantDiaryDetail = () => {
    const navigation = useNavigation();
    const router = useRoute();
    
    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, headerButton, webViewSendMessage} = useContext(ConfigContext);

    useEffect(() => {
    }, []);

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = async (webview): Promise<void> => {

        let userData = await getUserInfo();
        let myplantDiarySeq = router.params.myplantDiarySeq;
        let message = {
            key : 'webViewLoad',
            data : {
                myplantDiarySeq : myplantDiarySeq,
                userData : userData
            }
        }

        webViewSendMessage(webview, message);
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {

        // 삭제 완료 후 스크린 이동
        } else if (key === 'moveMyplantDiaryList') {
            navigation.navigate('MyPlantDiaryList', {myplantSeq: data.myplantSeq});
        }
    };

    // 헤더 수정 버튼 선택 시
    if (headerButton == 'MyPlantDiaryDetail') {
        let myplantSeq = router.params.myplantSeq;
        let myplantDiarySeq = router.params.myplantDiarySeq;

        setTimeout(function() {
            navigation.navigate('MyPlantDiaryUpdateForm', {myplantSeq: myplantSeq, myplantDiarySeq: myplantDiarySeq})
        })
    }

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/diary/detail'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantDiaryDetailWebview = ref)}
                onLoadEnd={e => webViewLoad(myplantDiaryDetailWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantDiaryDetail;