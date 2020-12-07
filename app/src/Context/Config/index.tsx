import React, {createContext, useState, useEffect} from 'react';
import {Alert, Platform} from 'react-native';
import PushNotification from 'react-native-push-notification'

interface Props {
  children: JSX.Element | Array<JSX.Element>;
}

/*
 * ConfigContext 컨텍스트
 */
const defaultContext: configContext = {
    webViewUrl : "http://192.168.35.142", //(개발)
};
const ConfigContext = createContext(defaultContext);

/*
 * ConfigContextProvider 프로바이더
 */
const ConfigContextProvider = ({children}: Props) => {
  
    // 웹뷰
    const webViewUrl = "http://192.168.35.142";

    const [headerButton, setHeaderButton] = useState(String);   // 헤더 버튼 선택

    const [modalVisible, setModalVisible] = useState(false);    // 모달 노출 여부
    const [selectMyplantSeq, setSelectMyplantSeq] = useState();
  
    useEffect(() => {

    }, []);

    // 웹뷰로 정보 전달
    const webViewSendMessage = async (webview, message): void => {
        if (webview) {
            webview.postMessage(JSON.stringify(message));
        } else {
            console.error('webview 객체가 없습니다.', message)
        }
    };

    // 컨펌창
    const confirmAlert = (message, callbackFunction) => {
        Alert.alert('', message,
            [
                {text: '예', onPress: () => callbackFunction()},
                {text: '아니오'},
            ],
            { cancelable: false }
        );
    };

    // 알림 설정
    const setPlantNotification = (data) => {
        if (data.myplantSeq != undefined) {

            let id = data.myplantSeq;
            let message = data.myplantName + ' 에게 물을 주세요';
            let hours = 0;
            let minutes = 0;
            let date = new Date(data.waterDay);
            date.setHours(hours);
            date.setMinutes(minutes);

            
            if (Platform.OS === 'ios') {

            // 안드로이드의 경우
            } else {
                PushNotification.localNotificationSchedule({
                    id: id,
                    message: message,
                    date: date,
                });
            }
        } else {
            console.error('알림 데이터가 없습니다.')
        }
    }

    // 알림 삭제
    const cancelLocalNotifications = (id) => {
        if (Platform.OS === 'ios') {
        } else {
            PushNotification.cancelLocalNotifications({id: id});
        }
    }

    // 알림 전체 삭제
    const cancelAllLocalNotifications = () => {
        if (Platform.OS === 'ios') {
        } else {
            PushNotification.cancelAllLocalNotifications();
        }
    }

    // 설정된 알림 데이터 가져오기
    const getLocalNotifications = () => {
        if (Platform.OS === 'ios') {
        } else {
            PushNotification.getScheduledLocalNotifications((data) => {
                for (let i=0; i<data.length; i++) {
                    console.log(data[i])
                }
            });
        }
    }

    return (
        <ConfigContext.Provider
            value={{
                webViewUrl,
                headerButton, setHeaderButton,
                modalVisible, setModalVisible,
                selectMyplantSeq, setSelectMyplantSeq,
                setPlantNotification,
                cancelLocalNotifications,
                cancelAllLocalNotifications,
                getLocalNotifications,
                webViewSendMessage,
                confirmAlert,
            }}>
            {children}
        </ConfigContext.Provider>
    );
};

export {ConfigContextProvider, ConfigContext};