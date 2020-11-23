import React from 'react';
import {
  StatusBar,
} from 'react-native';
import SplashScreen from 'react-native-splash-screen';

// 컨텍스트
import {ConfigContextProvider} from '~/Context/Config';
import {UserContextProvider} from '~/Context/User';

import Navigator from '~/Screens/Navigator';

const App = () => {
  // 스플래시 이미지 종료
  SplashScreen.hide();

  return (
    <>
      <ConfigContextProvider>
        <UserContextProvider> 
          <StatusBar barStyle="default" />
          <Navigator />
        </UserContextProvider>
      </ConfigContextProvider>
    </>
  );
};

export default App;