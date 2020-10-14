(function(){
    var open = document.getElementById('open');
    var close = document.getElementById('close');
    var mask = document.getElementById('mask');
    var modal = document.getElementById('modal');

    open.addEventListener('click',function(){
        // .hiddenクラスを削除しモーダルを表示する
        mask.className = '';
        modal.className = '';
        // モーダルをふわっと表示させる
        modal.animate([{opacity: '0'}, {opacity: '1'}], 500);
    });

    close.addEventListener('click',function(){
        // closeではマスクとモーダル画面を非表示にする
        // クラス名を再定義し、CSSを当て込む
        mask.className = 'hidden';
        modal.className = 'hidden';
    });

    // モーダル画面外をクリックしてもモーダル画面が閉じるようにする
    mask.addEventListener('click',function(){
        // closeと同じなので、closeのクリックイベントを呼び出せば良い
        close.click();
    });
})();