import React from 'react';
import {ActivityIndicator} from 'react-native';
import Styled from 'styled-components/native';

const Container = Styled.View`
  position: absolute;
  left: 50%;
  right: 0;
  top: 50%;
  bottom: 0;
  marginLeft: -40px;
  marginTop: -40px;
`;
const Image = Styled.Image`
    width: 80px;
    height: 80px;
`;

const Loading = () => {
  return (
    <Container>
        <Image source={require('~/Assets/Images/spinner.gif')}/>
    </Container>
  );
};

export default Loading;
