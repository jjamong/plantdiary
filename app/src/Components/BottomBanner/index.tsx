import React, {useEffect} from 'react';

import { BannerAd, BannerAdSize, TestIds  } from '@react-native-firebase/admob';

// 배너 ID
const adBannerUnitId = __DEV__ ? TestIds.BANNER : 'ca-app-pub-3588286886332636~6426522994'; // 광고 ID 입력 

const BottomBanner = () => {
  useEffect(() => {
  }, []);

  return (
    <>
      <BannerAd
          unitId={adBannerUnitId}
          size={BannerAdSize.FULL_BANNER}
          requestOptions={{
              requestNonPersonalizedAdsOnly: true,
          }}
          onAdLoaded={() => {
              //console.log('Advert loaded');
          }}
          onAdFailedToLoad={(error) => {
              console.error('Advert failed to load: ', error);
          }}
      />
    </>
  );
};

export default BottomBanner;