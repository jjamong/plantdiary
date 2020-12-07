import React, {useContext, useEffect} from 'react';
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
 * MyPlantDiaryForm 다이어리 폼 스크린
 */
const MyPlantDiaryForm = () => {
    const navigation = useNavigation();
    const router = useRoute();
    
    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, headerButton, webViewSendMessage,
        setPlantNotification
    } = useContext(ConfigContext);
    
    useEffect(() => {
    }, []);

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = async (): Promise<void> => {

        let userData = await getUserInfo();
        let myplantSeq;
        let myplantDiarySeq;
        if (router.params) {
            myplantSeq = router.params.myplantSeq;
            myplantDiarySeq = router.params.myplantDiarySeq;
        }

        let message = {
            key : 'webViewLoad',
            data : {
                myplantSeq : myplantSeq,
                myplantDiarySeq : myplantDiarySeq,
                userData : userData
            }
        }
        webViewSendMessage(myplantFormWebview, message);
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {
            
        // 저장 완료 후 리스트 스크린 이동
        } else if (key === 'insertSuccess') {
            if (data.notificationData) setPlantNotification(data.notificationData);
            navigation.navigate('MyPlantDiaryList', {myplantSeq: data.myplantSeq});

        // 수정 완료 후 리스트 스크린 이동
        } else if (key === 'updateSuccess') {
            if (data.notificationData) setPlantNotification(data.notificationData);
            navigation.navigate('MyPlantDiaryDetail', {myplantSeq: data.myplantSeq});
        }
    };

    // 헤더 등록 버튼 선택 시
    if (headerButton == 'MyPlantDiaryInsertForm') {
        let message = {
            key : 'insert',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    // 헤더 수정 버튼 선택 시
    } else if (headerButton == 'MyPlantDiaryUpdateForm') {
        let message = {
            key : 'update',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    }

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/diary/form'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantFormWebview = ref)}
                onLoadEnd={e => webViewLoad(myplantFormWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantDiaryForm;