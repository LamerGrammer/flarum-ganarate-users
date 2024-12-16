import app from 'flarum/admin/app';
import GenerateUsersPage from './components/GenerateUsersPage';

// app.initializers.add('imynely/generate-users', () => {
//   console.log('[imynely/generate-users] Hello, admin!');
// });

app.initializers.add('imynely/generate-users', () => {
  app.extensionData
    .for('imynely-generate-users')
    .registerPage(GenerateUsersPage);
});
