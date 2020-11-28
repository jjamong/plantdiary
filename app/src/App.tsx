import React from 'react';
import {
    StatusBar,
} from 'react-native';
import Styled from 'styled-components/native';
import SplashScreen from 'react-native-splash-screen';

// 컨텍스트
import {ConfigContextProvider} from '~/Context/Config';
import {UserContextProvider} from '~/Context/User';

import Navigator from '~/Screens/Navigator';

// 스타일 설정
const SafeAreaView = Styled.SafeAreaView`height: 100%;`

const App = () => {
  
  // 스플래시 이미지 종료
  SplashScreen.hide();

  return (
    <>
        <ConfigContextProvider>
            <UserContextProvider> 
                <StatusBar barStyle="default" />
                <SafeAreaView>
                    <Navigator />
                </SafeAreaView>
            </UserContextProvider>
        </ConfigContextProvider>
    </>
  );
};

export default App;