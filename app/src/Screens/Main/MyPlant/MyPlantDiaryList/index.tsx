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
 * MyPlant 내식물 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantDiaryList = () => {
    const navigation = useNavigation();
    const router = useRoute();

    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, webViewSendMessage} = useContext(ConfigContext);

    useEffect(() => {
        screenFocus();
    }, []);
  
    // 화면 포커스 시 실행되는 함수
    const screenFocus = (): void => {
        navigation.addListener('focus', () => {
            if (firstLoadCheck) return;
            //webViewLoad();
            myplantDiaryListWebview.reload();
        });
    };

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = async (webview): Promise<void> => {
        firstLoadCheck = false;

        let userData = await getUserInfo();
        let myplantSeq = router.params.myplantSeq;
        let message = {
            key : 'webViewLoad',
            data : {
                myplantSeq : myplantSeq,
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
        
        // 내식물 등록 페이지 이동
        } else if (key === 'moveMyplantDiaryForm') {
            navigation.navigate('MyPlantDiaryInsertForm', {myplantSeq: data.myplantSeq});

        // 다이어리 상세 페이지 이동
        } else if (key === 'moveMyplantDiaryDetail') {
            navigation.navigate('MyPlantDiaryDetail', {myplantSeq: data.myplantSeq, myplantDiarySeq: data.myplantDiarySeq});
        }
    };

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/diary/list'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantDiaryListWebview = ref)}
                onLoadEnd={e => webViewLoad(myplantDiaryListWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantDiaryList;