const fs = require('fs');
let content = fs.readFileSync('c:/xampp/htdocs/Khoá Luận Tốt Nghiệp/FE/corevue/src/pages/Admin/Dashboard.vue', 'utf8');

content = content.replace(
  'recentAdmins() {',
  'isSuperAdmin() {\n      const raw = localStorage.getItem(\'admin_data\');\n      if (raw) {\n        try {\n          const adminData = JSON.parse(raw);\n          return Number(adminData?.id_chuc_vu || adminData?.chuc_vu) === 1;\n        } catch(e) {}\n      }\n      return false;\n    },\n    recentAdmins() {'
);

const regex = /const \[usersRes, adminsRes\] = await Promise\.all\(\[\s*axios\.get\(USERS_API_URL, this\.authHeader\(\)\),\s*axios\.get\(ADMINS_API_URL, this\.authHeader\(\)\),\s*\]\)\s*this\.users = Array\.isArray\(usersRes\.data\?\.data\) \? usersRes\.data\.data : \[\]\s*this\.admins = Array\.isArray\(adminsRes\.data\?\.data\) \? adminsRes\.data\.data : \[\]/;

const replacement = `const promises = [axios.get(USERS_API_URL, this.authHeader())];
        if (this.isSuperAdmin) {
          promises.push(axios.get(ADMINS_API_URL, this.authHeader()));
        }

        const responses = await Promise.all(promises);
        this.users = Array.isArray(responses[0].data?.data) ? responses[0].data.data : [];
        if (this.isSuperAdmin && responses.length > 1) {
          this.admins = Array.isArray(responses[1].data?.data) ? responses[1].data.data : [];
        } else {
          this.admins = [];
        }`;

content = content.replace(regex, replacement);

fs.writeFileSync('c:/xampp/htdocs/Khoá Luận Tốt Nghiệp/FE/corevue/src/pages/Admin/Dashboard.vue', content, 'utf8');
console.log('Modified Dashboard.vue');
