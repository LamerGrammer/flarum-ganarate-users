import app from 'flarum/common/app';

app.initializers.add('imynely/generate-users', () => {
  console.log('[imynely/generate-users] Hello, forum and admin!');
});
